<?php

namespace App\Services\Notifications;

use App\Repositories\NotificationRuleRepository;
use App\Repositories\NotificationLogRepository;
use App\Models\NotificationRule;
use App\Models\NotificationTemplate;
use Illuminate\Support\Facades\Mail;
use App\Mail\GenericNotificationMail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class NotificationOrchestrator
{
	public function __construct(
		private NotificationRuleRepository $ruleRepo,
		private NotificationLogRepository $logRepo,
		private RecipientResolver $recipientResolver,
	) {}

	/**
	 * Handle a domain event and dispatch notifications.
	 * $payload must include: event_id (uuid), actor_user_id (optional), tenant_id (optional)
	 */
	public function handle(string $eventType, array $payload): void
	{
		$tenantId = $payload['tenant_id'] ?? null;
        Log::channel('maillog')->info('Notification event received', [
            'event_type' => $eventType,
            'payload_keys' => array_keys($payload),
        ]);
		$rules = $this->ruleRepo->getActiveRulesForEvent($eventType, $tenantId);
        if ($rules->count() > 0) {
            foreach ($rules as $rule) {
                $this->dispatchForRule($rule, $eventType, $payload);
            }
            return;
        }

        // Fallback: if no rules configured, notify actor (if provided)
        $actorId = $payload['actor_user_id'] ?? null;
        if ($actorId) {
            $actor = \App\Models\User::where('id', $actorId)->where('status', 1)->first();
            if ($actor) {
                $rule = new \App\Models\NotificationRule([
                    'id' => 0,
                    'name' => strtoupper(str_replace('.', ' ', $eventType)),
                    'event_type' => $eventType,
                    'channel' => 'email',
                ]);
                $template = new \App\Models\NotificationTemplate([
                    'subject' => 'Notification: {{event}}',
                    'body' => "Hello,\n\nReference: {{reference}}\n\nThanks.",
                    'channel' => 'email',
                ]);

                $log = $this->logRepo->create([
                    'uuid' => (string) Str::uuid(),
                    'event_id' => $payload['event_id'] ?? (string) Str::uuid(),
                    'rule_id' => null,
                    'recipient_user_id' => $actor->id,
                    'channel' => 'email',
                    'payload' => $payload,
                    'status' => 'queued',
                    'tenant_id' => $payload['tenant_id'] ?? null,
                ]);

                try {
                    [$subject, $body] = $this->renderEmail($rule, $template, $payload);
                    Mail::to($actor->email)->send(new GenericNotificationMail($subject, $body));
                    Log::channel('maillog')->info('Email sent (fallback)', [
                        'to' => $actor->email,
                        'subject' => $subject,
                        'event_type' => $eventType,
                        'rule_id' => null,
                        'log_uuid' => $log->uuid,
                    ]);
                    $log->subject = $subject;
                    $log->body = $body;
                    $this->logRepo->markSent($log);
                } catch (\Throwable $e) {
                    Log::channel('maillog')->error('Email failed (fallback)', [
                        'to' => $actor->email,
                        'subject' => $subject ?? null,
                        'event_type' => $eventType,
                        'error' => $e->getMessage(),
                    ]);
                    $this->logRepo->markFailed($log, $e->getMessage());
                }
            }
        }
	}

	private function dispatchForRule(NotificationRule $rule, string $eventType, array $payload): void
	{
		$template = $rule->template;
        $users = $this->recipientResolver->resolveUsers($rule, $payload);
        Log::channel('maillog')->info('Dispatching notifications for rule', [
            'event_type' => $eventType,
            'rule_id' => $rule->id,
            'recipients_count' => $users->count(),
        ]);
        // Always include actor in recipients if provided
        $actorId = $payload['actor_user_id'] ?? null;
        if ($actorId) {
            $alreadyIncluded = $users->contains(fn ($u) => (int) $u->id === (int) $actorId);
            if (!$alreadyIncluded) {
                $actor = \App\Models\User::where('id', $actorId)->where('status', 1)->first();
                if ($actor) {
                    $users->push($actor);
                }
            }
        }
		foreach ($users as $user) {
			// Idempotency: avoid duplicate sends for same event+rule+recipient
			$existing = \App\Models\NotificationLog::where([
				['event_id', '=', $payload['event_id'] ?? ''],
				['rule_id', '=', $rule->id],
				['recipient_user_id', '=', $user->id],
				['channel', '=', $rule->channel],
			])->first();
			if ($existing) {
				continue;
			}

			$log = $this->logRepo->create([
				'uuid' => (string) Str::uuid(),
				'event_id' => $payload['event_id'] ?? (string) Str::uuid(),
				'rule_id' => $rule->id,
				'recipient_user_id' => $user->id,
				'channel' => $rule->channel,
				'payload' => $payload,
				'status' => 'queued',
				'tenant_id' => $payload['tenant_id'] ?? null,
			]);

            try {
                if ($rule->channel === 'email') {
                    [$subject, $body] = $this->renderEmail($rule, $template, $payload);
                    Mail::to($user->email)->send(new GenericNotificationMail($subject, $body));
                    Log::channel('maillog')->info('Email sent', [
                        'to' => $user->email,
                        'subject' => $subject,
                        'event_type' => $eventType,
                        'rule_id' => $rule->id,
                        'log_uuid' => $log->uuid,
                    ]);
                    $log->subject = $subject;
                    $log->body = $body;
                    $this->logRepo->markSent($log);
                }
            } catch (\Throwable $e) {
                Log::channel('maillog')->error('Email failed', [
                    'to' => $user->email ?? null,
                    'subject' => $subject ?? null,
                    'event_type' => $eventType,
                    'rule_id' => $rule->id,
                    'error' => $e->getMessage(),
                ]);
				$this->logRepo->markFailed($log, $e->getMessage());
			}
		}
	}

    private function renderEmail(NotificationRule $rule, NotificationTemplate $template, array $payload): array
    {
        $subject = $rule->subject_override ?: ($template->subject ?? ('Notification: ' . $rule->name));
        $body = $template->body;

        // Build replacement variables from payload (scalars only)
        $vars = [];
        foreach ($payload as $key => $value) {
            if (is_scalar($value)) {
                $vars[$key] = (string) $value;
            }
        }

        // Provide sensible defaults for common placeholders
        $vars['event_type'] = $vars['event_type'] ?? ($rule->event_type ?? '');
        $vars['event'] = $vars['event'] ?? $vars['event_type'];
		$vars['reference'] = $vars['reference']
			?? ($vars['reference_number'] ?? $vars['order_number'] ?? $vars['sale_number'] ?? $vars['payment_number'] ?? $vars['id'] ?? '');

        // Derive entity and identifiers for convenience
        $entity = '';
        if (!empty($vars['event_type']) && str_contains($vars['event_type'], '.')) {
            $entity = (string) \Illuminate\Support\Str::before($vars['event_type'], '.');
        }
        $entityId = null;
        $entityName = null;
        switch ($entity) {
            case 'order':
                $entityId = $payload['order_id'] ?? null;
                $entityName = $payload['order_number'] ?? null;
                break;
            case 'sale':
                $entityId = $payload['sale_id'] ?? null;
                $entityName = $payload['sale_number'] ?? null;
                break;
            case 'payment':
                $entityId = $payload['payment_id'] ?? ($payload['id'] ?? null);
                $entityName = $payload['payment_number'] ?? null;
                break;
            case 'product':
                $entityId = $payload['product_id'] ?? null;
                $entityName = $payload['product_name'] ?? null;
                break;
            case 'customer':
                $entityId = $payload['customer_id'] ?? null;
                $entityName = $payload['customer_name'] ?? null;
                break;
            case 'supplier':
                $entityId = $payload['supplier_id'] ?? null;
                $entityName = $payload['supplier_name'] ?? null;
                break;
            case 'user':
                $entityId = $payload['user_id'] ?? null;
                $entityName = $payload['user_name'] ?? null;
                break;
            default:
                // leave nulls
                break;
        }
        $vars['entity'] = $vars['entity'] ?? $entity;
        $vars['entity_id'] = $vars['entity_id'] ?? ($entityId ? (string) $entityId : '');
        $vars['name'] = $vars['name']
            ?? ($entityName
                ?? ($vars['user_name'] ?? $vars['customer_name'] ?? $vars['supplier_name'] ?? $vars['product_name'] ?? ''));

        // Compute a link to the entity if possible
        $baseUrl = rtrim((string) config('app.url'), '/');
        $path = null;
        if ($entityId) {
            $paths = [
                'order' => '/orders/' . $entityId,
                'sale' => '/sales/' . $entityId,
                'payment' => '/payments/' . $entityId,
                'product' => '/products/' . $entityId,
                'customer' => '/customers/' . $entityId,
                'supplier' => '/suppliers/' . $entityId,
                'user' => '/user-management/' . $entityId,
            ];
            $path = $paths[$entity] ?? null;
        }
        $vars['link'] = $vars['link'] ?? ($path && $baseUrl ? $baseUrl . $path : '');

        // Resolve author/actor details if available
        $author = null;
        if (!empty($payload['actor_user_id'])) {
            $author = \App\Models\User::where('id', (int) $payload['actor_user_id'])->where('status', 1)->first();
        }
        if ($author) {
            $vars['author_name'] = $vars['author_name'] ?? ($author->name ?? '');
            $vars['author_email'] = $vars['author_email'] ?? ($author->email ?? '');
            $vars['author'] = $vars['author'] ?? trim(($author->name ?? '') . ($author->email ? ' <' . $author->email . '>' : ''));
            // Common aliases
            $vars['actor_name'] = $vars['actor_name'] ?? $vars['author_name'];
            $vars['actor_email'] = $vars['actor_email'] ?? $vars['author_email'];
            $vars['actor'] = $vars['actor'] ?? $vars['author'];
        } else {
            // Fallback to provided strings if any
            if (!empty($payload['actor_name']) || !empty($payload['actor_email'])) {
                $vars['author_name'] = $vars['author_name'] ?? ($payload['actor_name'] ?? '');
                $vars['author_email'] = $vars['author_email'] ?? ($payload['actor_email'] ?? '');
                $vars['author'] = $vars['author'] ?? trim(($payload['actor_name'] ?? '') . (!empty($payload['actor_email']) ? ' <' . $payload['actor_email'] . '>' : ''));
                $vars['actor_name'] = $vars['actor_name'] ?? $vars['author_name'];
                $vars['actor_email'] = $vars['actor_email'] ?? $vars['author_email'];
                $vars['actor'] = $vars['actor'] ?? $vars['author'];
            }
        }

        // Replace placeholders
        foreach ($vars as $key => $value) {
            $subject = str_replace('{{' . $key . '}}', (string) $value, $subject);
            $body = str_replace('{{' . $key . '}}', (string) $value, $body);
        }

        // Append details section to body for convenience
        $details = [];
        if (!empty($vars['reference'])) { $details[] = 'Reference: ' . $vars['reference']; }
        if (!empty($vars['name'])) { $details[] = 'Name: ' . $vars['name']; }
        if (!empty($vars['entity'])) { $details[] = 'Entity: ' . ucfirst($vars['entity']); }
        if (!empty($vars['entity_id'])) { $details[] = 'Entity ID: ' . $vars['entity_id']; }
        if (!empty($vars['author'])) { $details[] = 'Author: ' . $vars['author']; }
        if (!empty($vars['link'])) { $details[] = 'Link: ' . $vars['link']; }
        if (!empty($details)) {
            $body = rtrim($body) . "\n\n" . "Details:" . "\n" . implode("\n", array_map(fn($l) => '- ' . $l, $details)) . "\n";
        }

        return [$subject, $body];
    }
}


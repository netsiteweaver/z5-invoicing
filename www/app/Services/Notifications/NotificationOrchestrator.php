<?php

namespace App\Services\Notifications;

use App\Repositories\NotificationRuleRepository;
use App\Repositories\NotificationLogRepository;
use App\Models\NotificationRule;
use App\Models\NotificationTemplate;
use Illuminate\Support\Facades\Mail;
use App\Mail\GenericNotificationMail;
use Illuminate\Support\Str;

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
		$rules = $this->ruleRepo->getActiveRulesForEvent($eventType, $tenantId);
		foreach ($rules as $rule) {
			$this->dispatchForRule($rule, $eventType, $payload);
		}
	}

	private function dispatchForRule(NotificationRule $rule, string $eventType, array $payload): void
	{
		$template = $rule->template;
        $users = $this->recipientResolver->resolveUsers($rule, $payload);
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
                    $log->subject = $subject;
                    $log->body = $body;
                    $this->logRepo->markSent($log);
                }
            } catch (\Throwable $e) {
				$this->logRepo->markFailed($log, $e->getMessage());
			}
		}
	}

	private function renderEmail(NotificationRule $rule, NotificationTemplate $template, array $payload): array
	{
		$subject = $rule->subject_override ?: ($template->subject ?? ('Notification: ' . $rule->name));
		$body = $template->body;
		foreach ($payload as $key => $value) {
			if (is_scalar($value)) {
				$subject = str_replace('{{' . $key . '}}', (string) $value, $subject);
				$body = str_replace('{{' . $key . '}}', (string) $value, $body);
			}
		}
		return [$subject, $body];
	}
}


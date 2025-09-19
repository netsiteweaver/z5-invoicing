<?php

namespace App\Repositories;

use App\Models\NotificationRule;

class NotificationRuleRepository
{
	/**
	 * Return active rules matching event type exactly or with suffix wildcard (e.g. product.*)
	 */
	public function getActiveRulesForEvent(string $eventType, ?int $tenantId = null)
	{
		[$prefix, $wild] = $this->computeWildcard($eventType);
		return NotificationRule::with(['template', 'recipients'])
			->when($tenantId, function ($q) use ($tenantId) {
				$q->where(function ($q2) use ($tenantId) {
					$q2->whereNull('tenant_id')->orWhere('tenant_id', $tenantId);
				});
			})
			->where('is_active', true)
			->where(function ($q) use ($eventType, $prefix, $wild) {
				$q->where('event_type', $eventType)
					->orWhere('event_type', $wild)
					->orWhere(function ($q2) use ($prefix) {
						$q2->where('event_type', 'like', $prefix . '%')
							->where('event_type', 'like', '%.%');
					});
			})
			->get();
	}

	private function computeWildcard(string $eventType): array
	{
		$parts = explode('.', $eventType);
		$prefix = $parts[0] . '.'; // e.g., product.
		$wild = $parts[0] . '.*';
		return [$prefix, $wild];
	}
}


<?php

namespace App\Services\Notifications;

use App\Models\NotificationRule;
use App\Models\User;

class RecipientResolver
{
	/**
	 * Resolve recipients for a rule given payload. Returns collection of Users.
	 */
	public function resolveUsers(NotificationRule $rule, array $payload)
	{
		$userIds = collect();
		foreach ($rule->recipients()->where('is_active', true)->orderBy('priority')->get() as $rec) {
			if ($rec->recipient_type === 'user') {
				$userIds->push((int) $rec->recipient_value);
			} elseif ($rec->recipient_type === 'role') {
				$roleId = is_numeric($rec->recipient_value) ? (int) $rec->recipient_value : null;
				if ($roleId) {
					$ids = \DB::table('user_roles')->where('role_id', $roleId)->pluck('user_id');
					$userIds = $userIds->merge($ids);
				}
			} elseif ($rec->recipient_type === 'special' && $rec->recipient_value === 'actor') {
				if (!empty($payload['actor_user_id'])) {
					$userIds->push((int) $payload['actor_user_id']);
				}
			}
		}

		$userIds = $userIds->unique()->filter();
		return User::whereIn('id', $userIds)->where('status', 1)->get();
	}
}


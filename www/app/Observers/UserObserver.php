<?php

namespace App\Observers;

use App\Models\User;
use App\Services\Notifications\NotificationOrchestrator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class UserObserver
{
	public function __construct(private NotificationOrchestrator $orchestrator)
	{
	}

	public function created(User $user): void
	{
		$this->dispatch('user.created', $user);
	}

	public function updated(User $user): void
	{
		// Avoid notifying on auth-related updates (e.g., remember_token on logout/login)
		$changedKeys = array_keys($user->getChanges());
		$nonMeaningful = [
			'remember_token',
			'updated_at',
			'last_login',
			'ip',
			'token',
			'token_valid_until',
		];
		$meaningfulChanges = array_diff($changedKeys, $nonMeaningful);
		if (count($meaningfulChanges) === 0) {
			return;
		}
		$this->dispatch('user.updated', $user);
	}

	public function deleted(User $user): void
	{
		$this->dispatch('user.deleted', $user);
	}

	private function dispatch(string $eventType, User $user): void
	{
		$actorId = $user->updated_by ?? $user->created_by ?? Auth::id();
		$payload = [
			'event_id' => (string) Str::uuid(),
			'actor_user_id' => $actorId,
			'user_id' => $user->id,
			'user_name' => $user->name,
			'user_email' => $user->email,
			'actor_name' => optional(User::find($actorId))->name,
		];
		$this->orchestrator->handle($eventType, $payload);
	}
}


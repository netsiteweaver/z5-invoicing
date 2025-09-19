<?php

namespace App\Repositories;

use App\Models\NotificationLog;

class NotificationLogRepository
{
	public function create(array $attributes): NotificationLog
	{
		return NotificationLog::create($attributes);
	}

	public function markSent(NotificationLog $log): NotificationLog
	{
		$log->status = 'sent';
		$log->sent_at = now();
		$log->save();
		return $log;
	}

	public function markFailed(NotificationLog $log, string $error): NotificationLog
	{
		$log->status = 'failed';
		$log->error = $error;
		$log->attempt = ($log->attempt ?? 0) + 1;
		$log->save();
		return $log;
	}
}


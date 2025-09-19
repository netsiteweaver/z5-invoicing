<?php

namespace App\Services\Notifications;

class EventMatcher
{
	/**
	 * Check if an event pattern matches an event type.
	 * Supports patterns like 'product.*' and exact matches.
	 */
	public function matches(string $pattern, string $eventType): bool
	{
		if ($pattern === $eventType) {
			return true;
		}
		if (str_ends_with($pattern, '.*')) {
			$prefix = substr($pattern, 0, -1); // keep trailing dot
			return str_starts_with($eventType, rtrim($prefix, '*'));
		}
		return false;
	}
}


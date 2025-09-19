<?php

namespace App\Repositories;

use App\Models\NotificationTemplate;

class NotificationTemplateRepository
{
	public function findByName(string $name): ?NotificationTemplate
	{
		return NotificationTemplate::where('name', $name)->first();
	}

	public function getActiveByChannel(string $channel = 'email')
	{
		return NotificationTemplate::where('channel', $channel)
			->where('is_active', true)
			->get();
	}

	public function create(array $attributes): NotificationTemplate
	{
		return NotificationTemplate::create($attributes);
	}

	public function update(NotificationTemplate $template, array $attributes): NotificationTemplate
	{
		$template->fill($attributes);
		$template->save();
		return $template;
	}
}


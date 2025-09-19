<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class NotificationLog extends Model
{
	use HasFactory;

	protected $fillable = [
		'uuid',
		'event_id',
		'rule_id',
		'recipient_user_id',
		'channel',
		'subject',
		'body',
		'payload',
		'status',
		'error',
		'sent_at',
		'attempt',
		'tenant_id',
	];

	protected $casts = [
		'payload' => 'array',
		'sent_at' => 'datetime',
		'attempt' => 'integer',
	];

	protected static function boot()
	{
		parent::boot();
		static::creating(function ($model) {
			if (empty($model->uuid)) {
				$model->uuid = Str::uuid();
			}
		});
	}

	public function rule(): BelongsTo
	{
		return $this->belongsTo(NotificationRule::class, 'rule_id');
	}

	public function recipient(): BelongsTo
	{
		return $this->belongsTo(User::class, 'recipient_user_id');
	}
}


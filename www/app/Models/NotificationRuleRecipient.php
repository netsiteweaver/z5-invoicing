<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationRuleRecipient extends Model
{
	use HasFactory;

	protected $fillable = [
		'rule_id',
		'recipient_type',
		'recipient_value',
		'priority',
		'is_active',
	];

	protected $casts = [
		'priority' => 'integer',
		'is_active' => 'boolean',
	];

	public function rule(): BelongsTo
	{
		return $this->belongsTo(NotificationRule::class, 'rule_id');
	}
}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class NotificationRule extends Model
{
	use HasFactory;

	protected $fillable = [
		'uuid',
		'name',
		'event_type',
		'channel',
		'template_id',
		'subject_override',
		'is_active',
		'is_system',
		'tenant_id',
		'created_by',
		'updated_by',
	];

	protected $casts = [
		'is_active' => 'boolean',
		'is_system' => 'boolean',
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

	public function template(): BelongsTo
	{
		return $this->belongsTo(NotificationTemplate::class, 'template_id');
	}

	public function recipients(): HasMany
	{
		return $this->hasMany(NotificationRuleRecipient::class, 'rule_id');
	}

	public function createdBy(): BelongsTo
	{
		return $this->belongsTo(User::class, 'created_by');
	}

	public function updatedBy(): BelongsTo
	{
		return $this->belongsTo(User::class, 'updated_by');
	}
}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class NotificationTemplate extends Model
{
	use HasFactory;

	protected $fillable = [
		'uuid',
		'channel',
		'name',
		'subject',
		'body',
		'variables',
		'is_active',
		'created_by',
		'updated_by',
	];

	protected $casts = [
		'variables' => 'array',
		'is_active' => 'boolean',
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

 	public function createdBy(): BelongsTo
 	{
 		return $this->belongsTo(User::class, 'created_by');
 	}

 	public function updatedBy(): BelongsTo
 	{
 		return $this->belongsTo(User::class, 'updated_by');
 	}

 	public function rules(): HasMany
 	{
 		return $this->hasMany(NotificationRule::class, 'template_id');
 	}
}


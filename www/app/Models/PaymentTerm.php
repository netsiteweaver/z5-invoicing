<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class PaymentTerm extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'name',
        'type', // days|eom|manual
        'days',
        'description',
        'is_default',
        'created_by',
        'updated_by',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'days' => 'integer',
            'is_default' => 'boolean',
            'status' => 'integer',
        ];
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
            }
        });
    }

    // Relationships
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }
}




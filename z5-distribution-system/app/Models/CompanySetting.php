<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class CompanySetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'company_name',
        'legal_name',
        'brn',
        'vat_number',
        'address',
        'city',
        'postal_code',
        'country',
        'phone_primary',
        'phone_secondary',
        'email_primary',
        'email_secondary',
        'website',
        'logo_path',
        'description',
        'currency',
        'timezone',
        'date_format',
        'time_format',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
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
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Helper methods
    public function getFullAddressAttribute(): string
    {
        $addressParts = array_filter([
            $this->address,
            $this->city,
            $this->postal_code,
            $this->country
        ]);

        return implode(', ', $addressParts);
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->legal_name ?: $this->company_name;
    }

    public static function getCurrent(): ?self
    {
        return self::active()->first();
    }
}

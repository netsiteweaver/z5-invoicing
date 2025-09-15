<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'company_name',
        'legal_name',
        'contact_person',
        'brn',
        'vat',
        'full_name',
        'address',
        'city',
        'postal_code',
        'country',
        'phone_number1',
        // Virtual attributes accepted for mass-assignment and mapped via mutators
        'phone_number',
        'phone_number2',
        'email',
        'customer_type',
        'remarks',
        'notes',
        'created_by',
        'updated_by',
        'status',
    ];

    protected function casts(): array
    {
        return [
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
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('full_name');
    }

    public function scopeBusiness($query)
    {
        return $query->where('customer_type', 'business');
    }

    public function scopeIndividual($query)
    {
        return $query->where('customer_type', 'individual');
    }

    public function scopeByCity($query, $city)
    {
        return $query->where('city', $city);
    }

    // Accessors
    public function getDisplayNameAttribute(): string
    {
        return $this->customer_type === 'business' 
            ? $this->company_name 
            : $this->full_name;
    }

    public function getPrimaryPhoneAttribute(): string
    {
        return $this->phone_number1
            ?? $this->phone_number2
            ?? '';
    }

    // Map form-friendly attributes to database columns
    public function getPhoneNumberAttribute(): ?string
    {
        return $this->getAttributeValue('phone_number1');
    }

    public function setPhoneNumberAttribute($value): void
    {
        $this->attributes['phone_number1'] = $value;
    }

    public function getNotesAttribute(): ?string
    {
        return $this->getAttributeValue('remarks');
    }

    public function setNotesAttribute($value): void
    {
        $this->attributes['remarks'] = $value;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'sale_number',
        'order_id',
        'customer_id',
        'sale_date',
        'delivery_date',
        'due_date',
        'sale_status',
        'payment_status',
        'payment_method',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'shipping_amount',
        'total_amount',
        'currency',
        'notes',
        'internal_notes',
        'created_by',
        'updated_by',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'sale_date' => 'date',
            'delivery_date' => 'date',
            'due_date' => 'date',
            'subtotal' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'shipping_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
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
            if (empty($model->sale_number)) {
                $model->sale_number = 'SALE-' . strtoupper(Str::random(8));
            }
        });
    }

    // Relationships
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
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

    public function scopeByStatus($query, $status)
    {
        return $query->where('sale_status', $status);
    }

    public function scopeByPaymentStatus($query, $status)
    {
        return $query->where('payment_status', $status);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('sale_date', [$startDate, $endDate]);
    }

    public function scopeByCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    // Accessors
    public function getIsDraftAttribute(): bool
    {
        return $this->sale_status === 'draft';
    }

    public function getIsConfirmedAttribute(): bool
    {
        return in_array($this->sale_status, ['confirmed', 'processing', 'shipped', 'delivered']);
    }

    public function getIsCompletedAttribute(): bool
    {
        return $this->sale_status === 'delivered';
    }

    public function getIsCancelledAttribute(): bool
    {
        return $this->sale_status === 'cancelled';
    }

    public function getIsPaidAttribute(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->payment_status === 'overdue';
    }

    // Methods
    public function calculateTotals(): void
    {
        $this->subtotal = $this->items()->sum('line_total');
        $this->total_amount = $this->subtotal + $this->tax_amount + $this->shipping_amount - $this->discount_amount;
        $this->save();
    }

    public function canBeEdited(): bool
    {
        return in_array($this->sale_status, ['draft', 'pending']);
    }

    public function canBeCancelled(): bool
    {
        return !in_array($this->sale_status, ['delivered', 'cancelled']);
    }
}

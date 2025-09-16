<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'payment_date',
        'payment_number',
        'payment_type',
        'payment_method',
        'amount',
        'payment_type_id',
        'order_id',
        'sale_id',
        'customer_id',
        'reference_number',
        'notes',
        'due_date',
        'payment_status',
        'created_by',
        'updated_by',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'date',
            'amount' => 'decimal:2',
            'due_date' => 'date',
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
            if (empty($model->payment_number)) {
                $model->payment_number = 'PAY-' . strtoupper(Str::random(8));
            }
        });

        $recalc = function ($model) {
            try {
                if (!empty($model->sale_id) && $model->sale) {
                    $model->sale->refreshPaymentStatus();
                }
            } catch (\Throwable $e) {
                // swallow to avoid breaking save
            }
            try {
                if (!empty($model->order_id) && $model->order && method_exists($model->order, 'refreshPaymentStatus')) {
                    $model->order->refreshPaymentStatus();
                }
            } catch (\Throwable $e) {
                // swallow to avoid breaking save
            }
        };

        static::created($recalc);
        static::updated($recalc);
        static::deleted($recalc);
    }

    // Relationships
    public function paymentType(): BelongsTo
    {
        return $this->belongsTo(PaymentType::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
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

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('payment_status', $status);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('payment_type', $type);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('payment_date', [$startDate, $endDate]);
    }

    public function scopeByCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    // Accessors
    public function getIsPaidAttribute(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->payment_status === 'overdue';
    }

    public function getIsPendingAttribute(): bool
    {
        return $this->payment_status === 'pending';
    }
}

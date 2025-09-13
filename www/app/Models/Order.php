<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use App\Models\Param;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'order_number',
        'customer_id',
        'order_date',
        'delivery_date',
        'order_status',
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
            'order_date' => 'date',
            'delivery_date' => 'date',
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
            // Assign final sequential order number before insert (params-backed)
            if (empty($model->order_number)) {
                $prefix = Param::getValue('orders.prefix', 'ORD-');
                $padding = (int) (Param::getValue('orders.padding', '6'));
                // Ensure the counter is at least the max existing number
                $maxExisting = DB::table('orders')
                    ->select('order_number')
                    ->whereNotNull('order_number')
                    ->where('order_number', 'like', $prefix.'%')
                    ->orderByDesc('id')
                    ->value('order_number');
                if ($maxExisting) {
                    $numeric = (int) preg_replace('/\D+/', '', substr($maxExisting, strlen($prefix)));
                    $current = (int) (Param::getValue('orders.last_number', '0') ?? '0');
                    if ($numeric > $current) {
                        Param::setValue('orders.last_number', (string) $numeric);
                    }
                }
                // Generate a unique next number, retry if collision
                do {
                    $next = Param::incrementAndGet('orders.last_number', 0);
                    $candidate = $prefix . str_pad((string) $next, $padding, '0', STR_PAD_LEFT);
                } while (DB::table('orders')->where('order_number', $candidate)->exists());
                $model->order_number = $candidate;
            }
        });
    }

    // Relationships
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
        return $this->hasMany(OrderItem::class);
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

    public function scopeByStatus($query, $status)
    {
        return $query->where('order_status', $status);
    }

    public function scopeByPaymentStatus($query, $status)
    {
        return $query->where('payment_status', $status);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('order_date', [$startDate, $endDate]);
    }

    public function scopeByCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    // Accessors
    public function getIsDraftAttribute(): bool
    {
        return $this->order_status === 'draft';
    }

    public function getIsConfirmedAttribute(): bool
    {
        return in_array($this->order_status, ['confirmed', 'processing', 'shipped', 'delivered']);
    }

    public function getIsCompletedAttribute(): bool
    {
        return $this->order_status === 'delivered';
    }

    public function getIsCancelledAttribute(): bool
    {
        return $this->order_status === 'cancelled';
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
        return in_array($this->order_status, ['draft', 'pending']);
    }

    public function canBeCancelled(): bool
    {
        return !in_array($this->order_status, ['delivered', 'cancelled']);
    }
}

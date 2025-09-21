<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'order_id',
        'product_id',
        'uom_id',
        'uom_quantity',
        'quantity',
        'unit_price',
        'discount_percent',
        'discount_amount',
        'tax_type',
        'tax_percent',
        'tax_amount',
        'line_total',
        'notes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'discount_percent' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_percent' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'line_total' => 'decimal:2',
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
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function uom(): BelongsTo
    {
        return $this->belongsTo(Uom::class, 'uom_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    // Methods
    public function calculateLineTotal(): void
    {
        $subtotal = $this->quantity * $this->unit_price;
        $this->discount_amount = $subtotal * ($this->discount_percent / 100);

        // Derive tax_percent from tax_type if not explicitly set
        $taxPercent = $this->tax_percent;
        if ($taxPercent === null) {
            $taxPercent = match ($this->tax_type) {
                'standard' => 15,
                'zero' => 0,
                'exempt' => 0,
                default => 0,
            };
            $this->tax_percent = $taxPercent;
        }

        $this->tax_amount = ($subtotal - $this->discount_amount) * ($taxPercent / 100);
        $this->line_total = $subtotal - $this->discount_amount + $this->tax_amount;
        $this->save();
    }
}

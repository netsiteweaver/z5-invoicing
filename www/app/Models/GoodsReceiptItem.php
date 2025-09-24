<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class GoodsReceiptItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'goods_receipt_id',
        'product_id',
        'department_id',
        'quantity',
        'unit_cost',
        'uom',
        'uom_id',
        'uom_quantity',
        'batch_no',
        'expiry_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_cost' => 'decimal:2',
            'expiry_date' => 'date',
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

    public function goodsReceipt(): BelongsTo
    {
        return $this->belongsTo(GoodsReceipt::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function uom(): BelongsTo
    {
        return $this->belongsTo(Uom::class, 'uom_id');
    }

    // Helper methods for UOM management
    public function getBaseQuantityAttribute(): int
    {
        return \App\Services\UomService::toBaseUnits(
            $this->quantity, 
            $this->uom_id, 
            $this->uom_quantity
        );
    }

    public function getDisplayQuantityAttribute(): string
    {
        $uom = $this->uom;
        if (!$uom) {
            return $this->quantity . ' units';
        }
        
        return $this->quantity . ' ' . $uom->name;
    }

    public function getTotalCostAttribute(): float
    {
        return $this->quantity * $this->unit_cost;
    }

    public function getBaseUnitCostAttribute(): float
    {
        if ($this->base_quantity == 0) {
            return 0;
        }
        
        return $this->total_cost / $this->base_quantity;
    }

    // Convert this item's quantity to a different UOM
    public function convertToUom(Uom $targetUom): float
    {
        if (!$this->uom || !$this->uom->isCompatibleWith($targetUom)) {
            throw new \InvalidArgumentException('Cannot convert between incompatible UOMs');
        }

        return $this->uom->convertTo($this->quantity, $targetUom);
    }

    // Get available UOMs for this product's dimension
    public function getAvailableUoms()
    {
        if (!$this->product || !$this->product->uom) {
            return collect();
        }

        return Uom::byDimension($this->product->uom->dimension_code)
            ->active()
            ->ordered()
            ->get();
    }
}
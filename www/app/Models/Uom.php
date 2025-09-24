<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Uom extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'name',
        'code',
        'description',
        'units_per_uom',
        'dimension_code',
        'factor_to_base',
        'offset_to_base',
        'min_increment',
        'created_by',
        'updated_by',
        'status',
    ];

    // Dimension constants for better type safety
    const DIMENSION_COUNT = 'count';
    const DIMENSION_WEIGHT = 'weight';
    const DIMENSION_VOLUME = 'volume';
    const DIMENSION_LENGTH = 'length';

    // Base units for each dimension
    const BASE_UNITS = [
        self::DIMENSION_COUNT => 'unit',
        self::DIMENSION_WEIGHT => 'gram',
        self::DIMENSION_VOLUME => 'milliliter',
        self::DIMENSION_LENGTH => 'millimeter',
    ];

    protected function casts(): array
    {
        return [
            'units_per_uom' => 'integer',
            'factor_to_base' => 'decimal:12',
            'offset_to_base' => 'decimal:12',
            'min_increment' => 'decimal:12',
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

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function goodsReceiptItems(): HasMany
    {
        return $this->hasMany(GoodsReceiptItem::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('name');
    }

    public function scopeByDimension($query, string $dimension)
    {
        return $query->where('dimension_code', $dimension);
    }

    // Helper methods
    public function getDimensionNameAttribute(): string
    {
        return match($this->dimension_code) {
            self::DIMENSION_COUNT => 'Count',
            self::DIMENSION_WEIGHT => 'Weight',
            self::DIMENSION_VOLUME => 'Volume',
            self::DIMENSION_LENGTH => 'Length',
            default => 'Unknown'
        };
    }

    public function getBaseUnitAttribute(): string
    {
        return self::BASE_UNITS[$this->dimension_code] ?? 'unit';
    }

    public function isCompatibleWith(Uom $other): bool
    {
        return $this->dimension_code === $other->dimension_code;
    }

    public function convertTo(float $value, Uom $targetUom): float
    {
        if (!$this->isCompatibleWith($targetUom)) {
            throw new \InvalidArgumentException("Cannot convert between different dimensions: {$this->dimension_code} and {$targetUom->dimension_code}");
        }

        return \App\Services\UomService::convert($value, $this->id, $targetUom->id);
    }

    public function toBaseValue(float $value): float
    {
        return \App\Services\UomService::toBaseValue($value, $this->id);
    }

    public function fromBaseValue(float $baseValue): float
    {
        return \App\Services\UomService::fromBaseValue($baseValue, $this->id);
    }
}


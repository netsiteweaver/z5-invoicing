<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventory';

    protected $fillable = [
        'uuid',
        'product_id',
        'department_id',
        'current_stock',
        'min_stock_level',
        'max_stock_level',
        'reorder_point',
        'cost_price',
        'selling_price',
        'created_by',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'current_stock' => 'integer',
            'min_stock_level' => 'integer',
            'max_stock_level' => 'integer',
            'reorder_point' => 'integer',
            'cost_price' => 'decimal:2',
            'selling_price' => 'decimal:2',
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
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'product_id', 'product_id')
                    ->where('stock_movements.department_id', $this->department_id);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('current_stock', '<=', 'min_stock_level');
    }

    // Accessors
    public function getIsLowStockAttribute(): bool
    {
        return $this->current_stock <= $this->min_stock_level;
    }
}

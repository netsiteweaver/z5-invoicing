<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'product_id',
        'department_id',
        'movement_type',
        'quantity',
        'reference_type',
        'reference_id',
        'reference_number',
        'notes',
        'movement_date',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'movement_date' => 'datetime',
        ];
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
            }
            if (empty($model->movement_date)) {
                $model->movement_date = now();
            }
        });
    }

    // Relationships
    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('movement_type', $type);
    }

    public function scopeByInventory($query, $inventoryId)
    {
        return $query->where('inventory_id', $inventoryId);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('movement_date', [$startDate, $endDate]);
    }
}

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

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('movement_type', $type);
    }

    public function scopeByInventory($query, $inventoryId)
    {
        // Get inventory details to filter by product_id and department_id
        $inventory = Inventory::find($inventoryId);
        if ($inventory) {
            return $query->where('product_id', $inventory->product_id)
                        ->where('department_id', $inventory->department_id);
        }
        return $query->whereRaw('1=0'); // Return empty result if inventory not found
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('movement_date', [$startDate, $endDate]);
    }
}

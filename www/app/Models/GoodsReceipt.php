<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use App\Models\Param;

class GoodsReceipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'grn_number',
        'department_id',
        'receipt_date',
        'supplier_id',
        'supplier_name',
        'supplier_ref',
        'container_no',
        'bill_of_lading',
        'notes',
        'approval_status',
        'created_by',
        'updated_by',
        'status',
    ];

    protected $casts = [
        'receipt_date' => 'date',
        'status' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
            }
            if (empty($model->grn_number)) {
                $prefix = Param::getValue('gr.prefix', 'GRN-');
                $padding = (int) (Param::getValue('gr.padding', '6'));

                // Ensure last_number is at least max existing numeric
                $maxExisting = \Illuminate\Support\Facades\DB::table('goods_receipts')
                    ->select('grn_number')
                    ->whereNotNull('grn_number')
                    ->where('grn_number', 'like', $prefix.'%')
                    ->orderByDesc('id')
                    ->value('grn_number');
                if ($maxExisting) {
                    $numeric = (int) preg_replace('/\D+/', '', substr($maxExisting, strlen($prefix)));
                    $current = (int) (Param::getValue('gr.last_number', '0') ?? '0');
                    if ($numeric > $current) {
                        Param::setValue('gr.last_number', (string) $numeric);
                    }
                }

                // Generate next sequence, retry on collision
                do {
                    $next = Param::incrementAndGet('gr.last_number', 0);
                    $candidate = $prefix . str_pad((string) $next, $padding, '0', STR_PAD_LEFT);
                } while (\Illuminate\Support\Facades\DB::table('goods_receipts')->where('grn_number', $candidate)->exists());

                $model->grn_number = $candidate;
            }
        });
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
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
        return $this->hasMany(GoodsReceiptItem::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    // Accessor for status
    public function getStatusAttribute()
    {
        return $this->approval_status;
    }
}



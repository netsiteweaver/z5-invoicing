<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use App\Models\Param;

class StockTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'transfer_number',
        'from_department_id',
        'to_department_id',
        'transfer_date',
        'status',
        'notes',
        'created_by',
        'approved_by',
        'received_by',
    ];

    protected function casts(): array
    {
        return [
            'transfer_date' => 'date',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
            }
            if (empty($model->transfer_number)) {
                $prefix = Param::getValue('st.prefix', 'ST-');
                $padding = (int) (Param::getValue('st.padding', '6'));

                $maxExisting = \Illuminate\Support\Facades\DB::table('stock_transfers')
                    ->select('transfer_number')
                    ->whereNotNull('transfer_number')
                    ->where('transfer_number', 'like', $prefix.'%')
                    ->orderByDesc('id')
                    ->value('transfer_number');
                if ($maxExisting) {
                    $numeric = (int) preg_replace('/\D+/', '', substr($maxExisting, strlen($prefix)));
                    $current = (int) (Param::getValue('st.last_number', '0') ?? '0');
                    if ($numeric > $current) {
                        Param::setValue('st.last_number', (string) $numeric);
                    }
                }

                do {
                    $next = Param::incrementAndGet('st.last_number', 0);
                    $candidate = $prefix . str_pad((string) $next, $padding, '0', STR_PAD_LEFT);
                } while (\Illuminate\Support\Facades\DB::table('stock_transfers')->where('transfer_number', $candidate)->exists());

                $model->transfer_number = $candidate;
            }
        });
    }

    public function fromDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'from_department_id');
    }

    public function toDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'to_department_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockTransferItem::class);
    }
}



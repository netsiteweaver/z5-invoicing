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
				$prefix = \App\Models\Param::getValue('payments.prefix', 'PAY-');
				$padding = (int) (\App\Models\Param::getValue('payments.padding', '6'));
				$maxExisting = \Illuminate\Support\Facades\DB::table('payments')
					->select('payment_number')
					->whereNotNull('payment_number')
					->where('payment_number', 'like', $prefix.'%')
					->orderByDesc('id')
					->value('payment_number');
				if ($maxExisting) {
					$numeric = (int) preg_replace('/\D+/', '', substr($maxExisting, strlen($prefix)));
					$current = (int) (\App\Models\Param::getValue('payments.last_number', '0') ?? '0');
					if ($numeric > $current) {
						\App\Models\Param::setValue('payments.last_number', (string) $numeric);
					}
				}
				do {
					$next = \App\Models\Param::incrementAndGet('payments.last_number', 0);
					$candidate = $prefix . str_pad((string) $next, $padding, '0', STR_PAD_LEFT);
				} while (\Illuminate\Support\Facades\DB::table('payments')->where('payment_number', $candidate)->exists());
				$model->payment_number = $candidate;
			}
		});
	}

	// Relationships
	public function paymentType(): BelongsTo
	{
		return $this->belongsTo(PaymentType::class);
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

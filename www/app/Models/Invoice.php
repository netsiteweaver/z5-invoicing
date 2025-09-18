<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use App\Models\Param;

class Invoice extends Model
{
	use HasFactory;

	protected $fillable = [
		'uuid',
		'invoice_number',
		'customer_id',
		'sale_id',
		'invoice_date',
		'due_date',
		'status',
		'payment_status',
		'subtotal',
		'tax_amount',
		'discount_amount',
		'total_amount',
		'paid_amount',
		'balance_amount',
		'notes',
		'terms_conditions',
		'payment_terms',
		'created_by',
		'updated_by',
		'status_flag',
	];

	protected function casts(): array
	{
		return [
			'invoice_date' => 'date',
			'due_date' => 'date',
			'subtotal' => 'decimal:2',
			'tax_amount' => 'decimal:2',
			'discount_amount' => 'decimal:2',
			'total_amount' => 'decimal:2',
			'paid_amount' => 'decimal:2',
			'balance_amount' => 'decimal:2',
			'status_flag' => 'integer',
		];
	}

	protected static function boot()
	{
		parent::boot();
		static::creating(function ($model) {
			if (empty($model->uuid)) {
				$model->uuid = Str::uuid();
			}
			if (empty($model->invoice_number)) {
				$prefix = Param::getValue('invoices.prefix', 'INV-');
				$padding = (int) (Param::getValue('invoices.padding', '6'));
				do {
					$next = Param::incrementAndGet('invoices.last_number', 0);
					$candidate = $prefix . str_pad((string) $next, $padding, '0', STR_PAD_LEFT);
				} while (static::query()->where('invoice_number', $candidate)->exists());
				$model->invoice_number = $candidate;
			}
		});
	}

	public function customer(): BelongsTo
	{
		return $this->belongsTo(Customer::class);
	}

	public function sale(): BelongsTo
	{
		return $this->belongsTo(Sale::class);
	}

	public function items(): HasMany
	{
		return $this->hasMany(InvoiceItem::class);
	}
}


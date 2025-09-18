<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class InvoiceItem extends Model
{
	use HasFactory;

	protected $fillable = [
		'uuid',
		'invoice_id',
		'product_id',
		'product_name',
		'product_sku',
		'description',
		'quantity',
		'unit_price',
		'discount_percentage',
		'discount_amount',
		'tax_percentage',
		'tax_amount',
		'line_total',
		'created_by',
		'updated_by',
		'status_flag',
	];

	protected function casts(): array
	{
		return [
			'quantity' => 'integer',
			'unit_price' => 'decimal:2',
			'discount_percentage' => 'decimal:2',
			'discount_amount' => 'decimal:2',
			'tax_percentage' => 'decimal:2',
			'tax_amount' => 'decimal:2',
			'line_total' => 'decimal:2',
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
		});
	}

	public function invoice(): BelongsTo
	{
		return $this->belongsTo(Invoice::class);
	}

	public function product(): BelongsTo
	{
		return $this->belongsTo(Product::class);
	}
}


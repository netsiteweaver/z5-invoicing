<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryLayer extends Model
{
	use HasFactory;

	protected $fillable = [
		'product_id',
		'department_id',
		'quantity_remaining',
		'unit_cost',
		'source_type',
		'source_id',
	];
}



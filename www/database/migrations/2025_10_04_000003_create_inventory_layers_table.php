<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		if (!Schema::hasTable('inventory_layers')) {
			Schema::create('inventory_layers', function (Blueprint $table) {
				$table->id();
				$table->unsignedBigInteger('product_id');
				$table->unsignedBigInteger('department_id');
				$table->integer('quantity_remaining');
				$table->decimal('unit_cost', 15, 4)->nullable();
				$table->string('source_type')->nullable();
				$table->unsignedBigInteger('source_id')->nullable();
				$table->timestamps();

				$table->index(['product_id', 'department_id']);
				$table->index(['product_id', 'department_id', 'quantity_remaining'], 'inv_layers_pid_did_qty_idx');
				$table->index('created_at');
			});
		}
	}

	public function down(): void
	{
		if (Schema::hasTable('inventory_layers')) {
			Schema::drop('inventory_layers');
		}
	}
};



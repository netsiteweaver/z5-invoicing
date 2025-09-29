<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::table('orders', function (Blueprint $table) {
			$table->string('manual_invoice_number', 100)->nullable()->after('order_number');
			$table->index('manual_invoice_number');
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::table('orders', function (Blueprint $table) {
			$table->dropIndex(['manual_invoice_number']);
			$table->dropColumn('manual_invoice_number');
		});
	}
};



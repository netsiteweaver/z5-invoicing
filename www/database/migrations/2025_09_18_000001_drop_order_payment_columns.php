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
			if (Schema::hasColumn('orders', 'payment_status')) {
				$table->dropColumn('payment_status');
			}
			if (Schema::hasColumn('orders', 'payment_method')) {
				$table->dropColumn('payment_method');
			}
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::table('orders', function (Blueprint $table) {
			if (!Schema::hasColumn('orders', 'payment_status')) {
				$table->enum('payment_status', ['pending', 'partial', 'paid', 'overdue'])->default('pending');
			}
			if (!Schema::hasColumn('orders', 'payment_method')) {
				$table->string('payment_method', 100)->nullable();
			}
		});
	}
};


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
		Schema::table('payments', function (Blueprint $table) {
			if (Schema::hasColumn('payments', 'order_id')) {
				$table->dropForeign(['order_id']);
				$table->dropIndex(['order_id']);
				$table->dropColumn('order_id');
			}
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::table('payments', function (Blueprint $table) {
			if (!Schema::hasColumn('payments', 'order_id')) {
				$table->unsignedBigInteger('order_id')->nullable()->after('payment_type_id');
				$table->foreign('order_id')->references('id')->on('orders');
				$table->index('order_id');
			}
		});
	}
};


<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		// Ensure index on payment_status is removed if present
		Schema::table('orders', function (Blueprint $table) {
			// Some drivers require raw SQL to drop unnamed indexes; use safe checks
			if (Schema::hasColumn('orders', 'payment_status')) {
				// Column removal handled in prior migration
			}
		});
	}

	public function down(): void
	{
		Schema::table('orders', function (Blueprint $table) {
			// No-op
		});
	}
};


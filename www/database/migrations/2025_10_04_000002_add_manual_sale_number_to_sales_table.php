<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::table('sales', function (Blueprint $table) {
			$table->string('manual_sale_number', 100)->nullable()->after('sale_number');
			$table->index('manual_sale_number');
		});
	}

	public function down(): void
	{
		Schema::table('sales', function (Blueprint $table) {
			$table->dropIndex(['manual_sale_number']);
			$table->dropColumn('manual_sale_number');
		});
	}
};



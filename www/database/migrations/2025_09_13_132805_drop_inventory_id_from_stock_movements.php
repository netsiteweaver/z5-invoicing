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
        Schema::table('stock_movements', function (Blueprint $table) {
            if (Schema::hasColumn('stock_movements', 'inventory_id')) {
                // Drop FK and index first if they exist
                try { $table->dropForeign(['inventory_id']); } catch (\Throwable $e) {}
                try { $table->dropIndex(['inventory_id']); } catch (\Throwable $e) {}
                $table->dropColumn('inventory_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            if (!Schema::hasColumn('stock_movements', 'inventory_id')) {
                $table->unsignedBigInteger('inventory_id')->nullable();
            }
        });
    }
};

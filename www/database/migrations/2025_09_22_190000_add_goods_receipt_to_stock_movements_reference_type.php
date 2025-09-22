<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add 'goods_receipt' to the reference_type enum
        DB::statement("ALTER TABLE stock_movements MODIFY COLUMN reference_type ENUM('order', 'sale', 'purchase', 'transfer', 'adjustment', 'count', 'goods_receipt') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'goods_receipt' from the reference_type enum
        DB::statement("ALTER TABLE stock_movements MODIFY COLUMN reference_type ENUM('order', 'sale', 'purchase', 'transfer', 'adjustment', 'count') NULL");
    }
};

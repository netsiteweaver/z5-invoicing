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
            // Drop foreign key constraints first
            $table->dropForeign(['product_id']);
            $table->dropForeign(['department_id']);
            
            // Drop old columns
            $table->dropColumn(['product_id', 'department_id']);
            
            // Add new column if it doesn't exist
            if (!Schema::hasColumn('stock_movements', 'inventory_id')) {
                $table->unsignedBigInteger('inventory_id')->after('uuid');
                $table->foreign('inventory_id')->references('id')->on('inventory');
                $table->index('inventory_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            // Drop new column
            $table->dropForeign(['inventory_id']);
            $table->dropIndex(['inventory_id']);
            $table->dropColumn('inventory_id');
            
            // Add back old columns
            $table->unsignedBigInteger('product_id')->after('uuid');
            $table->unsignedBigInteger('department_id')->after('product_id');
            
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('department_id')->references('id')->on('departments');
            
            $table->index('product_id');
            $table->index('department_id');
        });
    }
};
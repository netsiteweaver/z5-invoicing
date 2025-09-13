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
            if (!Schema::hasColumn('stock_movements', 'product_id')) {
                $table->unsignedBigInteger('product_id')->after('uuid');
                $table->index('product_id');
                $table->foreign('product_id')->references('id')->on('products');
            }
            if (!Schema::hasColumn('stock_movements', 'department_id')) {
                $table->unsignedBigInteger('department_id')->after('product_id');
                $table->index('department_id');
                $table->foreign('department_id')->references('id')->on('departments');
            }
            if (Schema::hasColumn('stock_movements', 'inventory_id')) {
                // keep old data; column removal can be handled later if needed
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            if (Schema::hasColumn('stock_movements', 'product_id')) {
                $table->dropForeign(['product_id']);
                $table->dropIndex(['product_id']);
                $table->dropColumn('product_id');
            }
            if (Schema::hasColumn('stock_movements', 'department_id')) {
                $table->dropForeign(['department_id']);
                $table->dropIndex(['department_id']);
                $table->dropColumn('department_id');
            }
        });
    }
};

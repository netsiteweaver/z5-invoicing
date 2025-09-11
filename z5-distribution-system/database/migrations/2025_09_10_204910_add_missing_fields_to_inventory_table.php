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
        Schema::table('inventory', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('inventory', 'current_stock')) {
                $table->integer('current_stock')->default(0)->after('department_id');
            }
            if (!Schema::hasColumn('inventory', 'min_stock_level')) {
                $table->integer('min_stock_level')->default(0)->after('current_stock');
            }
            if (!Schema::hasColumn('inventory', 'max_stock_level')) {
                $table->integer('max_stock_level')->nullable()->after('min_stock_level');
            }
            if (!Schema::hasColumn('inventory', 'cost_price')) {
                $table->decimal('cost_price', 10, 2)->nullable()->after('max_stock_level');
            }
            if (!Schema::hasColumn('inventory', 'selling_price')) {
                $table->decimal('selling_price', 10, 2)->nullable()->after('cost_price');
            }
            
            // Add indexes
            $table->index('current_stock');
            $table->index('min_stock_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory', function (Blueprint $table) {
            // Drop the columns we added
            $table->dropColumn(['current_stock', 'min_stock_level', 'max_stock_level', 'cost_price', 'selling_price']);
            
            // Drop indexes
            $table->dropIndex(['current_stock']);
            $table->dropIndex(['min_stock_level']);
        });
    }
};
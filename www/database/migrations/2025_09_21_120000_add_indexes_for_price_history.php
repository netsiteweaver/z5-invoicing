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
        Schema::table('order_items', function (Blueprint $table) {
            // For product-level filtering
            $table->index(['product_id', 'status']);
        });

        Schema::table('orders', function (Blueprint $table) {
            // For customer and date sorting
            $table->index(['customer_id', 'order_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex(['product_id', 'status']);
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['customer_id', 'order_date']);
        });
    }
};


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
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('sale_id');
            $table->unsignedBigInteger('order_item_id')->nullable();
            $table->unsignedBigInteger('product_id');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('discount_percent', 5, 2)->default(0.00);
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->decimal('tax_percent', 5, 2)->default(0.00);
            $table->decimal('tax_amount', 10, 2)->default(0.00);
            $table->decimal('line_total', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->tinyInteger('status')->default(1);

            $table->foreign('sale_id')->references('id')->on('sales')->onDelete('cascade');
            $table->foreign('order_item_id')->references('id')->on('order_items');
            $table->foreign('product_id')->references('id')->on('products');

            $table->index('sale_id');
            $table->index('product_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_items');
    }
};

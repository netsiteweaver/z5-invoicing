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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('department_id');
            $table->enum('movement_type', ['in', 'out', 'transfer', 'adjustment', 'count']);
            $table->integer('quantity');
            $table->enum('reference_type', ['order', 'sale', 'purchase', 'transfer', 'adjustment', 'count'])->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('reference_number', 100)->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('movement_date');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('department_id')->references('id')->on('departments');
            $table->foreign('created_by')->references('id')->on('users');
            
            $table->index('product_id');
            $table->index('department_id');
            $table->index('movement_type');
            $table->index(['reference_type', 'reference_id']);
            $table->index('movement_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};

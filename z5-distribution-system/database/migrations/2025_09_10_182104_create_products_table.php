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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('stockref')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('cost_price', 10, 2);
            $table->decimal('selling_price', 10, 2);
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('brand_id');
            $table->enum('type', ['finished', 'semi', 'raw'])->default('finished');
            $table->string('size', 100)->nullable();
            $table->string('compartments', 100)->nullable();
            $table->string('color', 100)->nullable();
            $table->string('photo')->nullable();
            $table->unsignedBigInteger('uom_id')->default(1);
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->tinyInteger('status')->default(1);
            
            $table->foreign('category_id')->references('id')->on('product_categories');
            $table->foreign('brand_id')->references('id')->on('product_brands');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            
            $table->index('stockref');
            $table->index('name');
            $table->index('category_id');
            $table->index('brand_id');
            $table->index('type');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

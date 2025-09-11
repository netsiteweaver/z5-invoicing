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
        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('department_id');
            $table->integer('quantity')->default(0);
            $table->integer('reserved')->default(0);
            $table->integer('onorder')->default(0);
            $table->integer('reorder_point')->default(0);
            $table->integer('reorder_quantity')->default(0);
            $table->string('bin_location', 100)->nullable();
            $table->timestamp('last_updated')->useCurrent()->useCurrentOnUpdate();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->tinyInteger('status')->default(1);
            
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('department_id')->references('id')->on('departments');
            $table->foreign('created_by')->references('id')->on('users');
            
            $table->unique(['product_id', 'department_id']);
            $table->index('product_id');
            $table->index('department_id');
            $table->index('quantity');
            $table->index('reorder_point');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory');
    }
};

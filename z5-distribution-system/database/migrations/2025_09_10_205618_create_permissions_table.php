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
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name')->unique(); // e.g., 'customers.create', 'orders.edit'
            $table->string('display_name'); // e.g., 'Create Customers', 'Edit Orders'
            $table->text('description')->nullable();
            $table->string('module')->nullable(); // e.g., 'customers', 'orders', 'inventory'
            $table->string('action')->nullable(); // e.g., 'create', 'edit', 'delete', 'view'
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['module', 'action']);
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
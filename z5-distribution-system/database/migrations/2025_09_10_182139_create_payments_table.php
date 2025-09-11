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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->date('payment_date');
            $table->string('payment_number')->unique();
            $table->enum('payment_type', ['disbursement', 'receipt', 'refund', 'adjustment', 'other'])->default('receipt');
            $table->string('payment_method', 100);
            $table->decimal('amount', 10, 2);
            $table->unsignedBigInteger('payment_type_id');
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('sale_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('reference_number', 100)->nullable();
            $table->text('notes')->nullable();
            $table->date('due_date')->nullable();
            $table->enum('payment_status', ['pending', 'paid', 'partial', 'overdue', 'cancelled'])->default('pending');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->tinyInteger('status')->default(1);
            
            $table->foreign('payment_type_id')->references('id')->on('payment_types');
            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('sale_id')->references('id')->on('sales');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            
            $table->index('payment_number');
            $table->index('payment_type_id');
            $table->index('order_id');
            $table->index('sale_id');
            $table->index('customer_id');
            $table->index('payment_status');
            $table->index('payment_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

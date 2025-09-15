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
        Schema::create('goods_receipts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('grn_number', 50)->unique();
            $table->unsignedBigInteger('department_id');
            $table->date('receipt_date');
            $table->string('supplier_name')->nullable();
            $table->string('supplier_ref')->nullable();
            $table->string('container_no')->nullable();
            $table->string('bill_of_lading')->nullable();
            $table->text('notes')->nullable();
            $table->enum('approval_status', ['draft','submitted','approved','cancelled'])->default('approved');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->tinyInteger('status')->default(1);

            $table->foreign('department_id')->references('id')->on('departments');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');

            $table->index(['department_id']);
            $table->index(['receipt_date']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods_receipts');
    }
};



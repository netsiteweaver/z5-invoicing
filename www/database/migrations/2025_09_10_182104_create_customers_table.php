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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('company_name');
            $table->string('legal_name')->nullable();
            $table->string('brn', 50)->nullable();
            $table->string('vat', 50)->nullable();
            $table->string('full_name')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('phone_number1', 50);
            $table->string('phone_number2', 50)->nullable();
            $table->string('email')->nullable();
            $table->enum('customer_type', ['business', 'individual'])->default('business');
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->tinyInteger('status')->default(1);
            
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            
            $table->index('company_name');
            $table->index('email');
            $table->index('phone_number1');
            $table->index('city');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};

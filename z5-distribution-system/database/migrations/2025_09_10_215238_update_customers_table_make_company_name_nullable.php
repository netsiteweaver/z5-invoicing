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
        Schema::table('customers', function (Blueprint $table) {
            // Make company_name nullable for individual customers
            $table->string('company_name')->nullable()->change();
            
            // Make phone_number1 nullable since it's not always required
            $table->string('phone_number1', 50)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // Revert company_name to required
            $table->string('company_name')->nullable(false)->change();
            
            // Revert phone_number1 to required
            $table->string('phone_number1', 50)->nullable(false)->change();
        });
    }
};
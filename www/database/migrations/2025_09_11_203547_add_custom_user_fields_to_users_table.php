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
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('uuid')->unique()->after('id');
            $table->string('username')->unique()->after('email');
            $table->string('user_level')->default('User')->after('password');
            $table->string('job_title')->nullable()->after('user_level');
            $table->string('photo')->nullable();
            $table->unsignedBigInteger('store_id')->nullable();
            $table->string('landing_page')->nullable();
            $table->timestamp('last_login')->nullable();
            $table->string('ip')->nullable();
            $table->string('token')->nullable();
            $table->timestamp('token_valid_until')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};

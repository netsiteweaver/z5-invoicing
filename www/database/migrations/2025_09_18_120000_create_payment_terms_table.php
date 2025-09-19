<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_terms', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->enum('type', ['days', 'eom', 'manual'])->default('days');
            $table->integer('days')->nullable();
            $table->string('description')->nullable();
            $table->boolean('is_default')->default(false);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->tinyInteger('status')->default(1);

            $table->index('name');
            $table->index('type');
            $table->index('status');
        });

        // Add optional foreign key to sales if column exists later
        if (Schema::hasTable('sales') && !Schema::hasColumn('sales', 'payment_term_id')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->unsignedBigInteger('payment_term_id')->nullable()->after('payment_status');
                $table->foreign('payment_term_id')->references('id')->on('payment_terms');
                $table->index('payment_term_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('sales') && Schema::hasColumn('sales', 'payment_term_id')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->dropForeign(['payment_term_id']);
                $table->dropColumn('payment_term_id');
            });
        }
        Schema::dropIfExists('payment_terms');
    }
};




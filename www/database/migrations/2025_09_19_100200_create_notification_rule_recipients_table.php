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
        Schema::create('notification_rule_recipients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rule_id');
            $table->enum('recipient_type', ['user', 'role', 'group', 'special']);
            $table->string('recipient_value');
            $table->integer('priority')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Use a short custom name to avoid MySQL 64-char identifier limit
            $table->unique(['rule_id', 'recipient_type', 'recipient_value'], 'uniq_rule_recipient');
            $table->index('rule_id');
            $table->index('recipient_type');
            $table->foreign('rule_id')->references('id')->on('notification_rules')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_rule_recipients');
    }
};


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
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('event_id');
            $table->unsignedBigInteger('rule_id')->nullable();
            $table->unsignedBigInteger('recipient_user_id')->nullable();
            $table->string('channel', 32)->default('email');
            $table->string('subject')->nullable();
            $table->longText('body')->nullable();
            $table->json('payload')->nullable();
            $table->enum('status', ['queued', 'sent', 'failed', 'skipped', 'retrying'])->default('queued');
            $table->text('error')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->integer('attempt')->default(0);
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->timestamps();

            $table->index('event_id');
            $table->index('rule_id');
            $table->index('recipient_user_id');
            $table->index('status');
            $table->unique(['event_id', 'rule_id', 'recipient_user_id', 'channel'], 'uniq_event_rule_recipient_channel');
            $table->foreign('rule_id')->references('id')->on('notification_rules')->onDelete('set null');
            $table->foreign('recipient_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};


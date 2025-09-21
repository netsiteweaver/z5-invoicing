<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Best-effort: drop possible existing unique indexes on email/username with various names
        $candidates = [
            'users_email_unique',
            'users_username_unique',
            'email_unique',
            'username_unique',
        ];
        foreach ($candidates as $idx) {
            try { DB::statement("ALTER TABLE `users` DROP INDEX `{$idx}`"); } catch (\Throwable $e) {}
        }
        // Also attempt column-based drop (some engines name indexes after the column)
        try { DB::statement("ALTER TABLE `users` DROP INDEX `email`"); } catch (\Throwable $e) {}
        try { DB::statement("ALTER TABLE `users` DROP INDEX `username`"); } catch (\Throwable $e) {}

        Schema::table('users', function (Blueprint $table) {
            // Add composite unique indexes scoped by status (soft-deleted rows have status=0)
            $table->unique(['email', 'status'], 'uniq_users_email_status');
            $table->unique(['username', 'status'], 'uniq_users_username_status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Remove composite unique indexes
            try { $table->dropUnique('uniq_users_email_status'); } catch (\Throwable $e) {}
            try { $table->dropUnique('uniq_users_username_status'); } catch (\Throwable $e) {}
        });

        // Restore single-column unique indexes (ignore if already exist)
        try { DB::statement("ALTER TABLE `users` ADD UNIQUE `users_email_unique` (`email`)"); } catch (\Throwable $e) {}
        try { DB::statement("ALTER TABLE `users` ADD UNIQUE `users_username_unique` (`username`)"); } catch (\Throwable $e) {}
    }
};



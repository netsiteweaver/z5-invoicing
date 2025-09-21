<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'payment_status')) {
                $table->enum('payment_status', ['pending', 'partial', 'paid', 'overdue'])->default('pending');
                $table->index('payment_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'payment_status')) {
                // Drop index if it exists (Laravel default name convention)
                try { $table->dropIndex(['payment_status']); } catch (\Throwable $e) {}
                $table->dropColumn('payment_status');
            }
        });
    }
};



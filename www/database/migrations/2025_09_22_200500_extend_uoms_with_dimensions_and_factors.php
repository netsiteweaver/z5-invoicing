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
        Schema::table('uoms', function (Blueprint $table) {
            if (!Schema::hasColumn('uoms', 'dimension_code')) {
                $table->string('dimension_code', 30)->default('count')->after('units_per_uom');
            }
            if (!Schema::hasColumn('uoms', 'factor_to_base')) {
                $table->decimal('factor_to_base', 24, 12)->default(1)->after('dimension_code');
            }
            if (!Schema::hasColumn('uoms', 'offset_to_base')) {
                $table->decimal('offset_to_base', 24, 12)->default(0)->after('factor_to_base');
            }
            if (!Schema::hasColumn('uoms', 'min_increment')) {
                $table->decimal('min_increment', 24, 12)->nullable()->after('offset_to_base');
            }
            $table->index('dimension_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('uoms', function (Blueprint $table) {
            try { $table->dropIndex(['dimension_code']); } catch (\Throwable $e) {}
            try { $table->dropColumn(['dimension_code', 'factor_to_base', 'offset_to_base', 'min_increment']); } catch (\Throwable $e) {}
        });
    }
};


<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing UOMs with proper dimension codes and conversion factors
        $existingUoms = [
            'UNIT' => [
                'dimension_code' => 'count',
                'factor_to_base' => 1,
                'offset_to_base' => 0,
                'min_increment' => 1,
            ],
            'BOX10' => [
                'dimension_code' => 'count',
                'factor_to_base' => 1,
                'offset_to_base' => 0,
                'min_increment' => 1,
            ],
            'PACK100' => [
                'dimension_code' => 'count',
                'factor_to_base' => 1,
                'offset_to_base' => 0,
                'min_increment' => 1,
            ],
            'TEST' => [
                'dimension_code' => 'count',
                'factor_to_base' => 1,
                'offset_to_base' => 0,
                'min_increment' => 1,
            ],
            'TEST2' => [
                'dimension_code' => 'count',
                'factor_to_base' => 1,
                'offset_to_base' => 0,
                'min_increment' => 1,
            ],
            'KG' => [
                'dimension_code' => 'weight',
                'factor_to_base' => 1000,
                'offset_to_base' => 0,
                'min_increment' => 0.001,
            ],
            'G' => [
                'dimension_code' => 'weight',
                'factor_to_base' => 1,
                'offset_to_base' => 0,
                'min_increment' => 0.001,
            ],
        ];

        foreach ($existingUoms as $code => $updates) {
            DB::table('uoms')
                ->where('code', $code)
                ->update($updates);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reset dimension codes to default
        DB::table('uoms')->update([
            'dimension_code' => 'count',
            'factor_to_base' => 1,
            'offset_to_base' => 0,
            'min_increment' => null,
        ]);
    }
};
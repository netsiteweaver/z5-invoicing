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
        // Add sample UOMs for different dimensions
        $sampleUoms = [
            // Weight dimension
            [
                'uuid' => \Illuminate\Support\Str::uuid(),
                'name' => 'Gram',
                'code' => 'G',
                'description' => 'Base unit for weight',
                'units_per_uom' => 1,
                'dimension_code' => 'weight',
                'factor_to_base' => 1,
                'offset_to_base' => 0,
                'min_increment' => 0.001,
                'created_by' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => \Illuminate\Support\Str::uuid(),
                'name' => 'Kilogram',
                'code' => 'KG',
                'description' => '1000 grams',
                'units_per_uom' => 1,
                'dimension_code' => 'weight',
                'factor_to_base' => 1000,
                'offset_to_base' => 0,
                'min_increment' => 0.001,
                'created_by' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => \Illuminate\Support\Str::uuid(),
                'name' => 'Pound',
                'code' => 'LB',
                'description' => '453.592 grams',
                'units_per_uom' => 1,
                'dimension_code' => 'weight',
                'factor_to_base' => 453.592,
                'offset_to_base' => 0,
                'min_increment' => 0.001,
                'created_by' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Volume dimension
            [
                'uuid' => \Illuminate\Support\Str::uuid(),
                'name' => 'Milliliter',
                'code' => 'ML',
                'description' => 'Base unit for volume',
                'units_per_uom' => 1,
                'dimension_code' => 'volume',
                'factor_to_base' => 1,
                'offset_to_base' => 0,
                'min_increment' => 0.1,
                'created_by' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => \Illuminate\Support\Str::uuid(),
                'name' => 'Liter',
                'code' => 'L',
                'description' => '1000 milliliters',
                'units_per_uom' => 1,
                'dimension_code' => 'volume',
                'factor_to_base' => 1000,
                'offset_to_base' => 0,
                'min_increment' => 0.001,
                'created_by' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => \Illuminate\Support\Str::uuid(),
                'name' => 'Gallon',
                'code' => 'GAL',
                'description' => '3785.41 milliliters',
                'units_per_uom' => 1,
                'dimension_code' => 'volume',
                'factor_to_base' => 3785.41,
                'offset_to_base' => 0,
                'min_increment' => 0.001,
                'created_by' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Length dimension
            [
                'uuid' => \Illuminate\Support\Str::uuid(),
                'name' => 'Millimeter',
                'code' => 'MM',
                'description' => 'Base unit for length',
                'units_per_uom' => 1,
                'dimension_code' => 'length',
                'factor_to_base' => 1,
                'offset_to_base' => 0,
                'min_increment' => 0.1,
                'created_by' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => \Illuminate\Support\Str::uuid(),
                'name' => 'Centimeter',
                'code' => 'CM',
                'description' => '10 millimeters',
                'units_per_uom' => 1,
                'dimension_code' => 'length',
                'factor_to_base' => 10,
                'offset_to_base' => 0,
                'min_increment' => 0.01,
                'created_by' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => \Illuminate\Support\Str::uuid(),
                'name' => 'Meter',
                'code' => 'M',
                'description' => '1000 millimeters',
                'units_per_uom' => 1,
                'dimension_code' => 'length',
                'factor_to_base' => 1000,
                'offset_to_base' => 0,
                'min_increment' => 0.001,
                'created_by' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => \Illuminate\Support\Str::uuid(),
                'name' => 'Inch',
                'code' => 'IN',
                'description' => '25.4 millimeters',
                'units_per_uom' => 1,
                'dimension_code' => 'length',
                'factor_to_base' => 25.4,
                'offset_to_base' => 0,
                'min_increment' => 0.01,
                'created_by' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => \Illuminate\Support\Str::uuid(),
                'name' => 'Foot',
                'code' => 'FT',
                'description' => '304.8 millimeters',
                'units_per_uom' => 1,
                'dimension_code' => 'length',
                'factor_to_base' => 304.8,
                'offset_to_base' => 0,
                'min_increment' => 0.01,
                'created_by' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Area dimension
            [
                'uuid' => \Illuminate\Support\Str::uuid(),
                'name' => 'Square Millimeter',
                'code' => 'MM2',
                'description' => 'Base unit for area',
                'units_per_uom' => 1,
                'dimension_code' => 'area',
                'factor_to_base' => 1,
                'offset_to_base' => 0,
                'min_increment' => 0.01,
                'created_by' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => \Illuminate\Support\Str::uuid(),
                'name' => 'Square Centimeter',
                'code' => 'CM2',
                'description' => '100 square millimeters',
                'units_per_uom' => 1,
                'dimension_code' => 'area',
                'factor_to_base' => 100,
                'offset_to_base' => 0,
                'min_increment' => 0.01,
                'created_by' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => \Illuminate\Support\Str::uuid(),
                'name' => 'Square Meter',
                'code' => 'M2',
                'description' => '1,000,000 square millimeters',
                'units_per_uom' => 1,
                'dimension_code' => 'area',
                'factor_to_base' => 1000000,
                'offset_to_base' => 0,
                'min_increment' => 0.01,
                'created_by' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Time dimension
            [
                'uuid' => \Illuminate\Support\Str::uuid(),
                'name' => 'Second',
                'code' => 'SEC',
                'description' => 'Base unit for time',
                'units_per_uom' => 1,
                'dimension_code' => 'time',
                'factor_to_base' => 1,
                'offset_to_base' => 0,
                'min_increment' => 1,
                'created_by' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => \Illuminate\Support\Str::uuid(),
                'name' => 'Minute',
                'code' => 'MIN',
                'description' => '60 seconds',
                'units_per_uom' => 1,
                'dimension_code' => 'time',
                'factor_to_base' => 60,
                'offset_to_base' => 0,
                'min_increment' => 1,
                'created_by' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => \Illuminate\Support\Str::uuid(),
                'name' => 'Hour',
                'code' => 'HR',
                'description' => '3600 seconds',
                'units_per_uom' => 1,
                'dimension_code' => 'time',
                'factor_to_base' => 3600,
                'offset_to_base' => 0,
                'min_increment' => 1,
                'created_by' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insert sample UOMs only if they don't exist
        foreach ($sampleUoms as $uom) {
            $exists = DB::table('uoms')->where('code', $uom['code'])->exists();
            if (!$exists) {
                DB::table('uoms')->insert($uom);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove sample UOMs (keep the original ones)
        DB::table('uoms')->whereIn('code', [
            'G', 'KG', 'LB', 'ML', 'L', 'GAL', 'MM', 'CM', 'M', 'IN', 'FT', 
            'MM2', 'CM2', 'M2', 'SEC', 'MIN', 'HR'
        ])->delete();
    }
};
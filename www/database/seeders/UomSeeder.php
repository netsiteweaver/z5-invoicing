<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $uoms = [
            // Base Units (Pieces/Cans/Items)
            [
                'uuid' => Str::uuid(),
                'name' => 'Piece',
                'code' => 'PC',
                'description' => 'Individual piece/can/bottle',
                'units_per_uom' => 1,
                'dimension_code' => 'count',
                'factor_to_base' => 1.0,
                'offset_to_base' => 0.0,
                'min_increment' => 1.0,
                'created_by' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Boxes - Various Sizes
            [
                'uuid' => Str::uuid(),
                'name' => 'Box of 6',
                'code' => 'BOX6',
                'description' => 'Box containing 6 pieces',
                'units_per_uom' => 6,
                'dimension_code' => 'count',
                'factor_to_base' => 6.0,
                'offset_to_base' => 0.0,
                'min_increment' => 1.0,
                'created_by' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'Box of 10',
                'code' => 'BOX10',
                'description' => 'Box containing 10 pieces',
                'units_per_uom' => 10,
                'dimension_code' => 'count',
                'factor_to_base' => 10.0,
                'offset_to_base' => 0.0,
                'min_increment' => 1.0,
                'created_by' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'Box of 12',
                'code' => 'BOX12',
                'description' => 'Box containing 12 pieces',
                'units_per_uom' => 12,
                'dimension_code' => 'count',
                'factor_to_base' => 12.0,
                'offset_to_base' => 0.0,
                'min_increment' => 1.0,
                'created_by' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'Box of 24',
                'code' => 'BOX24',
                'description' => 'Box containing 24 pieces',
                'units_per_uom' => 24,
                'dimension_code' => 'count',
                'factor_to_base' => 24.0,
                'offset_to_base' => 0.0,
                'min_increment' => 1.0,
                'created_by' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Cartons - Based on common box configurations
            [
                'uuid' => Str::uuid(),
                'name' => 'Carton (10 boxes of 10)',
                'code' => 'CTN10X10',
                'description' => 'Carton containing 10 boxes, each with 10 pieces (100 total)',
                'units_per_uom' => 100,
                'dimension_code' => 'count',
                'factor_to_base' => 100.0,
                'offset_to_base' => 0.0,
                'min_increment' => 1.0,
                'created_by' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'Carton (20 boxes of 10)',
                'code' => 'CTN20X10',
                'description' => 'Carton containing 20 boxes, each with 10 pieces (200 total)',
                'units_per_uom' => 200,
                'dimension_code' => 'count',
                'factor_to_base' => 200.0,
                'offset_to_base' => 0.0,
                'min_increment' => 1.0,
                'created_by' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'Carton (10 boxes of 12)',
                'code' => 'CTN10X12',
                'description' => 'Carton containing 10 boxes, each with 12 pieces (120 total)',
                'units_per_uom' => 120,
                'dimension_code' => 'count',
                'factor_to_base' => 120.0,
                'offset_to_base' => 0.0,
                'min_increment' => 1.0,
                'created_by' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'Carton (20 boxes of 12)',
                'code' => 'CTN20X12',
                'description' => 'Carton containing 20 boxes, each with 12 pieces (240 total)',
                'units_per_uom' => 240,
                'dimension_code' => 'count',
                'factor_to_base' => 240.0,
                'offset_to_base' => 0.0,
                'min_increment' => 1.0,
                'created_by' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'Carton (20 boxes of 24)',
                'code' => 'CTN20X24',
                'description' => 'Carton containing 20 boxes, each with 24 pieces (480 total)',
                'units_per_uom' => 480,
                'dimension_code' => 'count',
                'factor_to_base' => 480.0,
                'offset_to_base' => 0.0,
                'min_increment' => 1.0,
                'created_by' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Weight-based UOMs (for bulk items like biscuits)
            [
                'uuid' => Str::uuid(),
                'name' => 'Gram',
                'code' => 'G',
                'description' => 'Gram (base weight unit)',
                'units_per_uom' => 1,
                'dimension_code' => 'weight',
                'factor_to_base' => 1.0,
                'offset_to_base' => 0.0,
                'min_increment' => 0.1,
                'created_by' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'Kilogram',
                'code' => 'KG',
                'description' => 'Kilogram (1000 grams)',
                'units_per_uom' => 1000,
                'dimension_code' => 'weight',
                'factor_to_base' => 1000.0,
                'offset_to_base' => 0.0,
                'min_increment' => 0.001,
                'created_by' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Volume-based UOMs (for beverages)
            [
                'uuid' => Str::uuid(),
                'name' => 'Milliliter',
                'code' => 'ML',
                'description' => 'Milliliter (base volume unit)',
                'units_per_uom' => 1,
                'dimension_code' => 'volume',
                'factor_to_base' => 1.0,
                'offset_to_base' => 0.0,
                'min_increment' => 1.0,
                'created_by' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'Liter',
                'code' => 'L',
                'description' => 'Liter (1000 milliliters)',
                'units_per_uom' => 1000,
                'dimension_code' => 'volume',
                'factor_to_base' => 1000.0,
                'offset_to_base' => 0.0,
                'min_increment' => 0.001,
                'created_by' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Clear existing UOMs first (optional - remove if you want to keep existing data)
        // DB::table('uoms')->truncate();
        
        // Insert UOMs, skip if code already exists
        foreach ($uoms as $uom) {
            DB::table('uoms')->updateOrInsert(
                ['code' => $uom['code']],
                $uom
            );
        }

        $this->command->info('UOMs seeded successfully!');
    }
}

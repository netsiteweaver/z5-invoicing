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
        // Remove unnecessary UOMs for food business
        // Keep only: count, weight, volume, length, area (for packaging)
        
        $unnecessaryUoms = [
            // Time dimension - not relevant for food business
            'SEC', 'MIN', 'HR',
        ];

        // Delete unnecessary UOMs (only if they're not being used)
        foreach ($unnecessaryUoms as $code) {
            $uom = DB::table('uoms')->where('code', $code)->first();
            if ($uom) {
                // Check if UOM is being used
                $isUsed = DB::table('goods_receipt_items')->where('uom_id', $uom->id)->exists() ||
                         DB::table('products')->where('uom_id', $uom->id)->exists() ||
                         DB::table('sale_items')->where('uom_id', $uom->id)->exists() ||
                         DB::table('order_items')->where('uom_id', $uom->id)->exists();
                
                if (!$isUsed) {
                    DB::table('uoms')->where('code', $code)->delete();
                    echo "Deleted unused UOM: {$code}\n";
                } else {
                    echo "Cannot delete UOM {$code} - it's being used\n";
                }
            }
        }

        // Update the dimension validation in the controller to remove time
        // This will be handled in the controller update
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-add the time UOMs if needed
        $timeUoms = [
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

        foreach ($timeUoms as $uom) {
            DB::table('uoms')->insert($uom);
        }
    }
};
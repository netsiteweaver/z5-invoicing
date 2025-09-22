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
        Schema::create('uoms', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name', 100);
            $table->string('code', 50)->unique();
            $table->string('description', 255)->nullable();
            $table->unsignedInteger('units_per_uom')->default(1);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->tinyInteger('status')->default(1);

            $table->index('name');
            $table->index('code');
            $table->index('status');
        });

        // Insert default UOMs to prevent foreign key constraint violations
        DB::table('uoms')->insert([
            [
                'id' => 1,
                'uuid' => \Illuminate\Support\Str::uuid(),
                'name' => 'Unit',
                'code' => 'UNIT',
                'description' => 'Base unit',
                'units_per_uom' => 1,
                'created_by' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'uuid' => \Illuminate\Support\Str::uuid(),
                'name' => 'Box of 10',
                'code' => 'BOX10',
                'description' => 'Box containing 10 units',
                'units_per_uom' => 10,
                'created_by' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'uuid' => \Illuminate\Support\Str::uuid(),
                'name' => 'Pack of 100',
                'code' => 'PACK100',
                'description' => 'Pack containing 100 units',
                'units_per_uom' => 100,
                'created_by' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uoms');
    }
};


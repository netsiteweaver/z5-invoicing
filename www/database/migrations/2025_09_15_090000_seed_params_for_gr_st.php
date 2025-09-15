<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Defaults for Goods Receipts numbering
        DB::table('params')->updateOrInsert(
            ['key' => 'gr.prefix'],
            ['value' => 'GRN-', 'meta' => json_encode(['description' => 'Goods Receipt prefix'])]
        );
        DB::table('params')->updateOrInsert(
            ['key' => 'gr.padding'],
            ['value' => '6', 'meta' => json_encode(['description' => 'Zero-padding for GR number'])]
        );
        DB::table('params')->updateOrInsert(
            ['key' => 'gr.last_number'],
            ['value' => '0', 'meta' => json_encode(['description' => 'Last GR sequence number'])]
        );

        // Defaults for Stock Transfers numbering
        DB::table('params')->updateOrInsert(
            ['key' => 'st.prefix'],
            ['value' => 'ST-', 'meta' => json_encode(['description' => 'Stock Transfer prefix'])]
        );
        DB::table('params')->updateOrInsert(
            ['key' => 'st.padding'],
            ['value' => '6', 'meta' => json_encode(['description' => 'Zero-padding for ST number'])]
        );
        DB::table('params')->updateOrInsert(
            ['key' => 'st.last_number'],
            ['value' => '0', 'meta' => json_encode(['description' => 'Last ST sequence number'])]
        );
    }

    public function down(): void
    {
        DB::table('params')->whereIn('key', [
            'gr.prefix', 'gr.padding', 'gr.last_number',
            'st.prefix', 'st.padding', 'st.last_number',
        ])->delete();
    }
};



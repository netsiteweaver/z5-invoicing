<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Param;

class ParamsSeeder extends Seeder
{
    public function run(): void
    {
        Param::setValue('orders.prefix', 'ORD-');
        Param::setValue('orders.padding', '6');
        // Initialize last number only if not present
        if (Param::getValue('orders.last_number') === null) {
            Param::setValue('orders.last_number', '0');
        }

        // Sales numbering params
        Param::setValue('sales.prefix', 'SAL-');
        Param::setValue('sales.padding', '6');
        if (Param::getValue('sales.last_number') === null) {
            Param::setValue('sales.last_number', '0');
        }
    }
}

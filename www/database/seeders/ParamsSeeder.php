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

        // Default product brand (optional). Set to an existing brand ID.
        if (Param::getValue('products.default_brand_id') === null) {
            Param::setValue('products.default_brand_id', null);
        }

        // Product numbering params
        Param::setValue('products.prefix', 'PROD-');
        Param::setValue('products.padding', '6');
        if (Param::getValue('products.last_number') === null) {
            Param::setValue('products.last_number', '0');
        }
    }
}

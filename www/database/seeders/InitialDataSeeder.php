<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Department;
use App\Models\ProductCategory;
use App\Models\ProductBrand;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Inventory;
use App\Models\PaymentType;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class InitialDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create root user
        $rootUser = User::firstOrCreate(
            ['email' => 'admin@z5distribution.com'],
            [
                'uuid' => Str::uuid(),
                'name' => 'System Administrator',
                'username' => 'admin',
                'password' => Hash::make('password'),
                'user_level' => 'Root',
                'job_title' => 'System Administrator',
                'created_by' => 1,
                'status' => 1,
            ]
        );

        // Create departments
        $mainDept = Department::create([
            'uuid' => Str::uuid(),
            'name' => 'Main Store',
            'description' => 'Main store location',
            'address' => '123 Main Street, Port Louis, Mauritius',
            'phone_number' => '+230 123 4567',
            'email' => 'main@z5distribution.com',
            'is_main' => true,
            'created_by' => $rootUser->id,
            'status' => 1,
        ]);

        $warehouseDept = Department::create([
            'uuid' => Str::uuid(),
            'name' => 'Warehouse',
            'description' => 'Storage warehouse',
            'address' => '456 Warehouse Road, Port Louis, Mauritius',
            'phone_number' => '+230 123 4568',
            'email' => 'warehouse@z5distribution.com',
            'is_main' => false,
            'created_by' => $rootUser->id,
            'status' => 1,
        ]);

        // Create payment types
        $paymentTypes = [
            ['name' => 'Cash', 'description' => 'Cash payment', 'is_default' => true, 'display_order' => 1],
            ['name' => 'Bank Transfer', 'description' => 'Bank transfer payment', 'is_default' => true, 'display_order' => 2],
            ['name' => 'MCB Juice', 'description' => 'MCB Juice mobile payment', 'is_default' => true, 'display_order' => 3],
            ['name' => 'MyT Money', 'description' => 'MyT Money mobile payment', 'is_default' => true, 'display_order' => 4],
            ['name' => 'Blink', 'description' => 'Blink mobile payment', 'is_default' => true, 'display_order' => 5],
        ];

        foreach ($paymentTypes as $type) {
            PaymentType::create([
                'uuid' => Str::uuid(),
                'name' => $type['name'],
                'description' => $type['description'],
                'is_default' => $type['is_default'],
                'display_order' => $type['display_order'],
                'created_by' => $rootUser->id,
                'status' => 1,
            ]);
        }

        // Create product categories
        $electronics = ProductCategory::create([
            'uuid' => Str::uuid(),
            'name' => 'Electronics',
            'description' => 'Electronic products and gadgets',
            'display_order' => 1,
            'created_by' => $rootUser->id,
            'status' => 1,
        ]);

        $clothing = ProductCategory::create([
            'uuid' => Str::uuid(),
            'name' => 'Clothing',
            'description' => 'Clothing and apparel',
            'display_order' => 2,
            'created_by' => $rootUser->id,
            'status' => 1,
        ]);

        $homeGarden = ProductCategory::create([
            'uuid' => Str::uuid(),
            'name' => 'Home & Garden',
            'description' => 'Home and garden products',
            'display_order' => 3,
            'created_by' => $rootUser->id,
            'status' => 1,
        ]);

        // Create product brands
        $genericBrand = ProductBrand::create([
            'uuid' => Str::uuid(),
            'name' => 'Generic',
            'description' => 'Generic brand products',
            'created_by' => $rootUser->id,
            'status' => 1,
        ]);

        $premiumBrand = ProductBrand::create([
            'uuid' => Str::uuid(),
            'name' => 'Premium',
            'description' => 'Premium brand products',
            'created_by' => $rootUser->id,
            'status' => 1,
        ]);

        // Create products
        $products = [
            [
                'stockref' => 'ELEC-001',
                'name' => 'Smartphone',
                'description' => 'Latest model smartphone',
                'cost_price' => 15000.00,
                'selling_price' => 20000.00,
                'category_id' => $electronics->id,
                'brand_id' => $premiumBrand->id,
                'type' => 'finished',
                'size' => '6.1 inch',
                'color' => 'Black',
            ],
            [
                'stockref' => 'ELEC-002',
                'name' => 'Laptop',
                'description' => 'High-performance laptop',
                'cost_price' => 45000.00,
                'selling_price' => 60000.00,
                'category_id' => $electronics->id,
                'brand_id' => $premiumBrand->id,
                'type' => 'finished',
                'size' => '15.6 inch',
                'color' => 'Silver',
            ],
            [
                'stockref' => 'CLOTH-001',
                'name' => 'T-Shirt',
                'description' => 'Cotton t-shirt',
                'cost_price' => 500.00,
                'selling_price' => 800.00,
                'category_id' => $clothing->id,
                'brand_id' => $genericBrand->id,
                'type' => 'finished',
                'size' => 'M',
                'color' => 'Blue',
            ],
            [
                'stockref' => 'HOME-001',
                'name' => 'Garden Tool Set',
                'description' => 'Complete garden tool set',
                'cost_price' => 2500.00,
                'selling_price' => 3500.00,
                'category_id' => $homeGarden->id,
                'brand_id' => $genericBrand->id,
                'type' => 'finished',
                'size' => 'Standard',
                'color' => 'Green',
            ],
        ];

        foreach ($products as $productData) {
            $product = Product::create([
                'uuid' => Str::uuid(),
                'stockref' => $productData['stockref'],
                'name' => $productData['name'],
                'description' => $productData['description'],
                'cost_price' => $productData['cost_price'],
                'selling_price' => $productData['selling_price'],
                'category_id' => $productData['category_id'],
                'brand_id' => $productData['brand_id'],
                'type' => $productData['type'],
                'size' => $productData['size'],
                'color' => $productData['color'],
                'created_by' => $rootUser->id,
                'status' => 1,
            ]);

            // Create inventory for each product in both departments
            foreach ([$mainDept, $warehouseDept] as $dept) {
                Inventory::create([
                    'uuid' => Str::uuid(),
                    'product_id' => $product->id,
                    'department_id' => $dept->id,
                    'quantity' => rand(10, 50),
                    'reorder_point' => 5,
                    'reorder_quantity' => 20,
                    'bin_location' => 'A' . rand(1, 10),
                    'created_by' => $rootUser->id,
                    'status' => 1,
                ]);
            }
        }

        // Create customers
        $customers = [
            [
                'company_name' => 'ABC Trading Ltd',
                'legal_name' => 'ABC Trading Limited',
                'brn' => 'C123456789',
                'vat' => 'VAT123456789',
                'address' => '789 Business Street, Port Louis',
                'city' => 'Port Louis',
                'phone_number1' => '+230 234 5678',
                'email' => 'contact@abctrading.mu',
                'customer_type' => 'business',
            ],
            [
                'company_name' => 'John Smith',
                'full_name' => 'John Smith',
                'address' => '123 Residential Road, Curepipe',
                'city' => 'Curepipe',
                'phone_number1' => '+230 345 6789',
                'email' => 'john.smith@email.com',
                'customer_type' => 'individual',
            ],
        ];

        foreach ($customers as $customerData) {
            Customer::create([
                'uuid' => Str::uuid(),
                'company_name' => $customerData['company_name'],
                'legal_name' => $customerData['legal_name'] ?? null,
                'brn' => $customerData['brn'] ?? null,
                'vat' => $customerData['vat'] ?? null,
                'full_name' => $customerData['full_name'] ?? null,
                'address' => $customerData['address'],
                'city' => $customerData['city'],
                'phone_number1' => $customerData['phone_number1'],
                'email' => $customerData['email'],
                'customer_type' => $customerData['customer_type'],
                'created_by' => $rootUser->id,
                'status' => 1,
            ]);
        }

        // Create sample orders
        $customers = Customer::all();
        $products = Product::all();

        for ($i = 1; $i <= 5; $i++) {
            $customer = $customers->random();
            $order = Order::create([
                'uuid' => Str::uuid(),
                'order_number' => 'ORD-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'customer_id' => $customer->id,
                'order_date' => now()->subDays(rand(1, 30)),
                'delivery_date' => now()->addDays(rand(1, 7)),
                'order_status' => ['draft', 'pending', 'confirmed', 'processing', 'shipped', 'delivered'][rand(0, 5)],
                'payment_status' => ['pending', 'partial', 'paid', 'overdue'][rand(0, 3)],
                'payment_method' => 'Bank Transfer',
                'subtotal' => 0,
                'total_amount' => 0,
                'currency' => 'MUR',
                'notes' => 'Sample order ' . $i,
                'created_by' => $rootUser->id,
                'status' => 1,
            ]);

            // Add items to order
            $numItems = rand(1, 3);
            $subtotal = 0;
            
            for ($j = 0; $j < $numItems; $j++) {
                $product = $products->random();
                $quantity = rand(1, 5);
                $unitPrice = $product->selling_price;
                $lineTotal = $quantity * $unitPrice;
                $subtotal += $lineTotal;

                OrderItem::create([
                    'uuid' => Str::uuid(),
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'line_total' => $lineTotal,
                    'status' => 1,
                ]);
            }

            $order->update([
                'subtotal' => $subtotal,
                'total_amount' => $subtotal,
            ]);
        }
    }
}

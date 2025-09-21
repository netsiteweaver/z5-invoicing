<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define all permissions
        $permissions = [
            // Dashboard
            ['name' => 'dashboard.view', 'display_name' => 'View Dashboard', 'module' => 'dashboard', 'action' => 'view'],
            
            // Departments
            ['name' => 'departments.view', 'display_name' => 'View Departments', 'module' => 'departments', 'action' => 'view'],
            ['name' => 'departments.create', 'display_name' => 'Create Departments', 'module' => 'departments', 'action' => 'create'],
            ['name' => 'departments.edit', 'display_name' => 'Edit Departments', 'module' => 'departments', 'action' => 'edit'],
            ['name' => 'departments.delete', 'display_name' => 'Delete Departments', 'module' => 'departments', 'action' => 'delete'],

            // Customers
            ['name' => 'customers.view', 'display_name' => 'View Customers', 'module' => 'customers', 'action' => 'view'],
            ['name' => 'customers.create', 'display_name' => 'Create Customers', 'module' => 'customers', 'action' => 'create'],
            ['name' => 'customers.edit', 'display_name' => 'Edit Customers', 'module' => 'customers', 'action' => 'edit'],
            ['name' => 'customers.delete', 'display_name' => 'Delete Customers', 'module' => 'customers', 'action' => 'delete'],
            
            // Products
            ['name' => 'products.view', 'display_name' => 'View Products', 'module' => 'products', 'action' => 'view'],
            ['name' => 'products.create', 'display_name' => 'Create Products', 'module' => 'products', 'action' => 'create'],
            ['name' => 'products.edit', 'display_name' => 'Edit Products', 'module' => 'products', 'action' => 'edit'],
            ['name' => 'products.delete', 'display_name' => 'Delete Products', 'module' => 'products', 'action' => 'delete'],
            
            // Product Categories
            ['name' => 'product_categories.view', 'display_name' => 'View Product Categories', 'module' => 'product_categories', 'action' => 'view'],
            ['name' => 'product_categories.create', 'display_name' => 'Create Product Categories', 'module' => 'product_categories', 'action' => 'create'],
            ['name' => 'product_categories.edit', 'display_name' => 'Edit Product Categories', 'module' => 'product_categories', 'action' => 'edit'],
            ['name' => 'product_categories.delete', 'display_name' => 'Delete Product Categories', 'module' => 'product_categories', 'action' => 'delete'],
            
            // Product Brands
            ['name' => 'product_brands.view', 'display_name' => 'View Product Brands', 'module' => 'product_brands', 'action' => 'view'],
            ['name' => 'product_brands.create', 'display_name' => 'Create Product Brands', 'module' => 'product_brands', 'action' => 'create'],
            ['name' => 'product_brands.edit', 'display_name' => 'Edit Product Brands', 'module' => 'product_brands', 'action' => 'edit'],
            ['name' => 'product_brands.delete', 'display_name' => 'Delete Product Brands', 'module' => 'product_brands', 'action' => 'delete'],
            
            // Orders
            ['name' => 'orders.view', 'display_name' => 'View Orders', 'module' => 'orders', 'action' => 'view'],
            ['name' => 'orders.create', 'display_name' => 'Create Orders', 'module' => 'orders', 'action' => 'create'],
            ['name' => 'orders.edit', 'display_name' => 'Edit Orders', 'module' => 'orders', 'action' => 'edit'],
            ['name' => 'orders.delete', 'display_name' => 'Delete Orders', 'module' => 'orders', 'action' => 'delete'],
            ['name' => 'orders.convert_to_sale', 'display_name' => 'Convert Orders to Sales', 'module' => 'orders', 'action' => 'convert_to_sale'],
            
            // Sales
            ['name' => 'sales.view', 'display_name' => 'View Sales', 'module' => 'sales', 'action' => 'view'],
            ['name' => 'sales.create', 'display_name' => 'Create Sales', 'module' => 'sales', 'action' => 'create'],
            ['name' => 'sales.edit', 'display_name' => 'Edit Sales', 'module' => 'sales', 'action' => 'edit'],
            ['name' => 'sales.delete', 'display_name' => 'Delete Sales', 'module' => 'sales', 'action' => 'delete'],
            
            // Invoices
            ['name' => 'invoices.view', 'display_name' => 'View Invoices', 'module' => 'invoices', 'action' => 'view'],
            ['name' => 'invoices.create', 'display_name' => 'Create Invoices', 'module' => 'invoices', 'action' => 'create'],
            ['name' => 'invoices.edit', 'display_name' => 'Edit Invoices', 'module' => 'invoices', 'action' => 'edit'],
            ['name' => 'invoices.delete', 'display_name' => 'Delete Invoices', 'module' => 'invoices', 'action' => 'delete'],
            
            // Inventory
            ['name' => 'inventory.view', 'display_name' => 'View Inventory', 'module' => 'inventory', 'action' => 'view'],
            ['name' => 'inventory.create', 'display_name' => 'Create Inventory', 'module' => 'inventory', 'action' => 'create'],
            ['name' => 'inventory.edit', 'display_name' => 'Edit Inventory', 'module' => 'inventory', 'action' => 'edit'],
            ['name' => 'inventory.delete', 'display_name' => 'Delete Inventory', 'module' => 'inventory', 'action' => 'delete'],
            ['name' => 'inventory.stock_movement', 'display_name' => 'Record Stock Movements', 'module' => 'inventory', 'action' => 'stock_movement'],
            ['name' => 'inventory.low_stock', 'display_name' => 'View Low Stock Alerts', 'module' => 'inventory', 'action' => 'low_stock'],
            ['name' => 'inventory.stock_report', 'display_name' => 'View Stock Reports', 'module' => 'inventory', 'action' => 'stock_report'],

            // Goods Receipts
            ['name' => 'goods_receipts.view', 'display_name' => 'View Goods Receipts', 'module' => 'goods_receipts', 'action' => 'view'],
            ['name' => 'goods_receipts.create', 'display_name' => 'Create Goods Receipts', 'module' => 'goods_receipts', 'action' => 'create'],
            ['name' => 'goods_receipts.edit', 'display_name' => 'Edit Goods Receipts', 'module' => 'goods_receipts', 'action' => 'edit'],
            ['name' => 'goods_receipts.delete', 'display_name' => 'Delete Goods Receipts', 'module' => 'goods_receipts', 'action' => 'delete'],
            ['name' => 'goods_receipts.approve', 'display_name' => 'Approve Goods Receipts', 'module' => 'goods_receipts', 'action' => 'approve'],
            ['name' => 'goods_receipts.print', 'display_name' => 'Print Goods Receipts', 'module' => 'goods_receipts', 'action' => 'print'],

            // Stock Transfers
            ['name' => 'stock_transfers.view', 'display_name' => 'View Stock Transfers', 'module' => 'stock_transfers', 'action' => 'view'],
            ['name' => 'stock_transfers.create', 'display_name' => 'Create Stock Transfers', 'module' => 'stock_transfers', 'action' => 'create'],
            ['name' => 'stock_transfers.edit', 'display_name' => 'Edit Stock Transfers', 'module' => 'stock_transfers', 'action' => 'edit'],
            ['name' => 'stock_transfers.delete', 'display_name' => 'Delete Stock Transfers', 'module' => 'stock_transfers', 'action' => 'delete'],
            ['name' => 'stock_transfers.approve', 'display_name' => 'Approve Stock Transfers', 'module' => 'stock_transfers', 'action' => 'approve'],
            ['name' => 'stock_transfers.print', 'display_name' => 'Print Stock Transfers', 'module' => 'stock_transfers', 'action' => 'print'],
            
            // User Management
            ['name' => 'users.view', 'display_name' => 'View Users', 'module' => 'users', 'action' => 'view'],
            ['name' => 'users.create', 'display_name' => 'Create Users', 'module' => 'users', 'action' => 'create'],
            ['name' => 'users.edit', 'display_name' => 'Edit Users', 'module' => 'users', 'action' => 'edit'],
            ['name' => 'users.delete', 'display_name' => 'Delete Users', 'module' => 'users', 'action' => 'delete'],
            
            // Role Management
            ['name' => 'roles.view', 'display_name' => 'View Roles', 'module' => 'roles', 'action' => 'view'],
            ['name' => 'roles.create', 'display_name' => 'Create Roles', 'module' => 'roles', 'action' => 'create'],
            ['name' => 'roles.edit', 'display_name' => 'Edit Roles', 'module' => 'roles', 'action' => 'edit'],
            ['name' => 'roles.delete', 'display_name' => 'Delete Roles', 'module' => 'roles', 'action' => 'delete'],
            
            // Permission Management
            ['name' => 'permissions.view', 'display_name' => 'View Permissions', 'module' => 'permissions', 'action' => 'view'],
            ['name' => 'permissions.manage', 'display_name' => 'Manage Permissions', 'module' => 'permissions', 'action' => 'manage'],
            
            // Settings
            ['name' => 'settings.view', 'display_name' => 'View Settings', 'module' => 'settings', 'action' => 'view'],
            ['name' => 'settings.update', 'display_name' => 'Update Settings', 'module' => 'settings', 'action' => 'update'],

            // UOMs
            ['name' => 'uoms.view', 'display_name' => 'View UOMs', 'module' => 'uoms', 'action' => 'view'],
            ['name' => 'uoms.create', 'display_name' => 'Create UOMs', 'module' => 'uoms', 'action' => 'create'],
            ['name' => 'uoms.edit', 'display_name' => 'Edit UOMs', 'module' => 'uoms', 'action' => 'edit'],
            ['name' => 'uoms.delete', 'display_name' => 'Delete UOMs', 'module' => 'uoms', 'action' => 'delete'],
            
            // Reports
            ['name' => 'reports.view', 'display_name' => 'View Reports', 'module' => 'reports', 'action' => 'view'],
            ['name' => 'reports.sales', 'display_name' => 'View Sales Reports', 'module' => 'reports', 'action' => 'sales'],
            ['name' => 'reports.inventory', 'display_name' => 'View Inventory Reports', 'module' => 'reports', 'action' => 'inventory'],
        ];

        // Create permissions
        foreach ($permissions as $permissionData) {
            Permission::firstOrCreate(
                ['name' => $permissionData['name']],
                $permissionData
            );
        }

        // Create roles
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            [
                'display_name' => 'Administrator',
                'description' => 'Full system access',
                'is_system' => true,
            ]
        );

        $managerRole = Role::firstOrCreate(
            ['name' => 'manager'],
            [
                'display_name' => 'Manager',
                'description' => 'Management level access',
                'is_system' => true,
            ]
        );

        $salesRepRole = Role::firstOrCreate(
            ['name' => 'sales_rep'],
            [
                'display_name' => 'Sales Representative',
                'description' => 'Sales focused access',
                'is_system' => true,
            ]
        );

        $inventoryRole = Role::firstOrCreate(
            ['name' => 'inventory_manager'],
            [
                'display_name' => 'Inventory Manager',
                'description' => 'Inventory management access',
                'is_system' => true,
            ]
        );

        // Assign permissions to roles
        $this->assignPermissionsToRole($adminRole, Permission::all());
        
        $this->assignPermissionsToRole($managerRole, [
            'dashboard.view',
            'departments.view', 'departments.create', 'departments.edit',
            'customers.view', 'customers.create', 'customers.edit',
            'products.view', 'products.create', 'products.edit',
            'product_categories.view', 'product_categories.create', 'product_categories.edit',
            'product_brands.view', 'product_brands.create', 'product_brands.edit',
            'orders.view', 'orders.create', 'orders.edit', 'orders.convert_to_sale',
            'sales.view', 'sales.create', 'sales.edit',
            'inventory.view', 'inventory.create', 'inventory.edit', 'inventory.stock_movement',
            'inventory.low_stock', 'inventory.stock_report',
            'goods_receipts.view', 'goods_receipts.create', 'goods_receipts.edit', 'goods_receipts.approve', 'goods_receipts.print',
            'stock_transfers.view', 'stock_transfers.create', 'stock_transfers.edit', 'stock_transfers.approve', 'stock_transfers.print',
            'reports.view', 'reports.sales', 'reports.inventory',
            'uoms.view', 'uoms.create', 'uoms.edit',
        ]);

        $this->assignPermissionsToRole($salesRepRole, [
            'dashboard.view',
            'departments.view',
            'customers.view', 'customers.create', 'customers.edit',
            'products.view',
            'orders.view', 'orders.create', 'orders.edit', 'orders.convert_to_sale',
            'sales.view', 'sales.create', 'sales.edit',
            'inventory.view',
            'reports.view', 'reports.sales',
        ]);

        $this->assignPermissionsToRole($inventoryRole, [
            'dashboard.view',
            'departments.view',
            'products.view',
            'inventory.view', 'inventory.create', 'inventory.edit', 'inventory.stock_movement',
            'inventory.low_stock', 'inventory.stock_report',
            'goods_receipts.view', 'goods_receipts.create', 'goods_receipts.edit', 'goods_receipts.approve', 'goods_receipts.print',
            'stock_transfers.view', 'stock_transfers.create', 'stock_transfers.edit', 'stock_transfers.approve', 'stock_transfers.print',
            'reports.view', 'reports.inventory', 'uoms.view',
        ]);

        // Assign admin role to existing admin user
        $adminUser = User::where('email', 'admin@z5distribution.com')->first();
        if ($adminUser) {
            $adminUser->assignRole($adminRole);
        }
    }

    private function assignPermissionsToRole($role, $permissions)
    {
        $permissionModels = collect($permissions)->map(function ($permissionName) {
            return Permission::where('name', $permissionName)->first();
        })->filter();

        $role->syncPermissions($permissionModels);
    }
}
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
            
            // Inventory
            ['name' => 'inventory.view', 'display_name' => 'View Inventory', 'module' => 'inventory', 'action' => 'view'],
            ['name' => 'inventory.create', 'display_name' => 'Create Inventory', 'module' => 'inventory', 'action' => 'create'],
            ['name' => 'inventory.edit', 'display_name' => 'Edit Inventory', 'module' => 'inventory', 'action' => 'edit'],
            ['name' => 'inventory.delete', 'display_name' => 'Delete Inventory', 'module' => 'inventory', 'action' => 'delete'],
            ['name' => 'inventory.stock_movement', 'display_name' => 'Record Stock Movements', 'module' => 'inventory', 'action' => 'stock_movement'],
            ['name' => 'inventory.low_stock', 'display_name' => 'View Low Stock Alerts', 'module' => 'inventory', 'action' => 'low_stock'],
            ['name' => 'inventory.stock_report', 'display_name' => 'View Stock Reports', 'module' => 'inventory', 'action' => 'stock_report'],
            
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
            'customers.view', 'customers.create', 'customers.edit',
            'products.view', 'products.create', 'products.edit',
            'product_categories.view', 'product_categories.create', 'product_categories.edit',
            'product_brands.view', 'product_brands.create', 'product_brands.edit',
            'orders.view', 'orders.create', 'orders.edit', 'orders.convert_to_sale',
            'sales.view', 'sales.create', 'sales.edit',
            'inventory.view', 'inventory.create', 'inventory.edit', 'inventory.stock_movement',
            'inventory.low_stock', 'inventory.stock_report',
            'reports.view', 'reports.sales', 'reports.inventory',
        ]);

        $this->assignPermissionsToRole($salesRepRole, [
            'dashboard.view',
            'customers.view', 'customers.create', 'customers.edit',
            'products.view',
            'orders.view', 'orders.create', 'orders.edit', 'orders.convert_to_sale',
            'sales.view', 'sales.create', 'sales.edit',
            'inventory.view',
            'reports.view', 'reports.sales',
        ]);

        $this->assignPermissionsToRole($inventoryRole, [
            'dashboard.view',
            'products.view',
            'inventory.view', 'inventory.create', 'inventory.edit', 'inventory.stock_movement',
            'inventory.low_stock', 'inventory.stock_report',
            'reports.view', 'reports.inventory',
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
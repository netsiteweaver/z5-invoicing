<?php

namespace App\Helpers;

use App\Models\CompanySetting;

class TitleHelper
{
    /**
     * Generate dynamic page title based on controller and company name
     */
    public static function generate($pageTitle = null, $controller = null): string
    {
        $companySettings = CompanySetting::getCurrent();
        $companyName = $companySettings ? $companySettings->company_name : config('app.name', 'Laravel');
        
        // If no page title provided, try to generate from controller
        if (!$pageTitle && $controller) {
            $pageTitle = self::generateFromController($controller);
        }
        
        // Default page title
        if (!$pageTitle) {
            $pageTitle = 'Dashboard';
        }
        
        return "{$pageTitle} - {$companyName}";
    }
    
    /**
     * Generate page title from controller name
     */
    private static function generateFromController($controller): string
    {
        $controllerMap = [
            'DashboardController' => 'Dashboard',
            'CustomerController' => 'Customers',
            'ProductController' => 'Products',
            'ProductCategoryController' => 'Product Categories',
            'ProductBrandController' => 'Product Brands',
            'OrderController' => 'Orders',
            'SaleController' => 'Sales',
            'InventoryController' => 'Inventory',
            'UserManagementController' => 'User Management',
            'SettingsController' => 'Company Settings',
            'ProfileController' => 'Profile',
        ];
        
        $controllerName = class_basename($controller);
        return $controllerMap[$controllerName] ?? 'Page';
    }
    
    /**
     * Generate title with action
     */
    public static function generateWithAction($controller, $action = null): string
    {
        $baseTitle = self::generateFromController($controller);
        
        $actionMap = [
            'index' => '',
            'create' => 'Create ',
            'edit' => 'Edit ',
            'show' => 'View ',
            'store' => 'Create ',
            'update' => 'Update ',
            'destroy' => 'Delete ',
        ];
        
        $actionPrefix = $actionMap[$action] ?? '';
        
        return $actionPrefix . $baseTitle;
    }
    
    /**
     * Generate breadcrumb title (shorter version)
     */
    public static function generateBreadcrumb($pageTitle = null, $controller = null): string
    {
        if (!$pageTitle && $controller) {
            $pageTitle = self::generateFromController($controller);
        }
        
        return $pageTitle ?: 'Dashboard';
    }
}

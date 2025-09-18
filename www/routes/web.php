<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductBrandController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ChangelogController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Prevent route parameter conflicts: enforce numeric IDs for user management
Route::pattern('user_management', '[0-9]+');
Route::pattern('role', '[0-9]+');

Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication routes
require __DIR__.'/auth.php';




// Public changelog feed (no auth)
Route::get('/changelog', [ChangelogController::class, 'feed'])->name('changelog.feed');

// Protected routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Customer routes
    Route::resource('customers', CustomerController::class);
    
    // Product routes
    Route::resource('products', ProductController::class);
    
    // Product Category routes
    Route::resource('product-categories', ProductCategoryController::class);
    
    // Product Brand routes
    Route::resource('product-brands', ProductBrandController::class);
    
    // Order routes
    Route::resource('orders', OrderController::class);
    Route::get('orders/{order}/convert-to-sale', [OrderController::class, 'convertToSale'])->name('orders.convert-to-sale');
    Route::post('orders/{order}/convert', [OrderController::class, 'convertToSale'])->name('orders.convert');
    
    // Sales routes
    Route::resource('sales', SaleController::class);
    // Invoice routes
    Route::resource('invoices', InvoiceController::class)->except(['create', 'store', 'edit', 'update', 'destroy']);
    Route::post('invoices/create-from-sale/{sale}', [InvoiceController::class, 'createFromSale'])->name('invoices.create-from-sale');
    // Supplier routes
    Route::resource('suppliers', SupplierController::class);

    Route::post('sales/convert-from-order/{order}', [SaleController::class, 'convertFromOrder'])->name('sales.convert-from-order');
    
    // Inventory routesiki
    Route::resource('inventory', InventoryController::class);
    Route::post('inventory/{inventory}/stock-movement', [InventoryController::class, 'stockMovement'])->name('inventory.stock-movement');
    Route::get('inventory/low-stock', [InventoryController::class, 'lowStock'])->name('inventory.low-stock');
    Route::get('inventory/stock-report', [InventoryController::class, 'stockReport'])->name('inventory.stock-report');

    // Goods Receipt routes
    Route::resource('goods-receipts', \App\Http\Controllers\GoodsReceiptController::class);
    Route::get('goods-receipts/{goods_receipt}/print', [\App\Http\Controllers\GoodsReceiptController::class, 'print'])->name('goods-receipts.print')->middleware('permission:goods_receipts.print');
    Route::post('goods-receipts/{goods_receipt}/approve', [\App\Http\Controllers\GoodsReceiptController::class, 'approve'])->name('goods-receipts.approve')->middleware('permission:goods_receipts.approve');

    // Stock Transfer routes
    Route::resource('stock-transfers', \App\Http\Controllers\StockTransferController::class);
    Route::get('stock-transfers/{stock_transfer}/print', [\App\Http\Controllers\StockTransferController::class, 'print'])->name('stock-transfers.print')->middleware('permission:stock_transfers.print');
    Route::post('stock-transfers/{stock_transfer}/receive', [\App\Http\Controllers\StockTransferController::class, 'receive'])->name('stock-transfers.receive')->middleware('permission:stock_transfers.approve');
    
    // User Management routes (specific first to avoid conflict with resource catch-all)
    Route::get('user-management/roles', [UserManagementController::class, 'roles'])->name('user-management.roles');
    Route::get('user-management/roles/create', [UserManagementController::class, 'createRole'])->name('user-management.roles.create');
    Route::post('user-management/roles', [UserManagementController::class, 'storeRole'])->name('user-management.roles.store');
    Route::get('user-management/roles/{role}', [UserManagementController::class, 'showRole'])->name('user-management.roles.show');
    Route::get('user-management/roles/{role}/edit', [UserManagementController::class, 'editRole'])->name('user-management.roles.edit');
    Route::put('user-management/roles/{role}', [UserManagementController::class, 'updateRole'])->name('user-management.roles.update');
    Route::delete('user-management/roles/{role}', [UserManagementController::class, 'destroyRole'])->name('user-management.roles.destroy');
    Route::patch('user-management/roles/{role}/toggle', [UserManagementController::class, 'toggleRoleStatus'])->name('user-management.roles.toggle');
    Route::get('user-management/permissions', [UserManagementController::class, 'permissions'])->name('user-management.permissions');
    Route::patch('user-management/permissions/{permission}/toggle', [UserManagementController::class, 'togglePermissionStatus'])->name('user-management.permissions.toggle');
    Route::resource('user-management', UserManagementController::class)->except(['destroy']);
    Route::delete('user-management/{user_management}', [UserManagementController::class, 'destroy'])->name('user-management.destroy');
    
    // Settings routes
    Route::resource('settings', SettingsController::class)->except(['show', 'destroy']);
    Route::get('settings/current', [SettingsController::class, 'getCurrent'])->name('settings.current');
    Route::post('settings/{setting}/logo', [SettingsController::class, 'updateLogo'])->name('settings.logo');

    // (moved) changelog feed is public

    // User Manual (HTML) - serves file from project docs
    Route::get('/manual', function () {
        $path = base_path('..' . DIRECTORY_SEPARATOR . 'docs' . DIRECTORY_SEPARATOR . 'User Manual v1.0.html');
        if (!file_exists($path)) {
            abort(404);
        }
        return response()->file($path, [
            'Content-Type' => 'text/html; charset=UTF-8',
        ]);
    })->name('manual');

});

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\LoginActivity;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            // Get dashboard statistics with safe fallbacks
            $stats = [
                'total_orders' => Order::active()->count() ?? 0,
                'total_sales' => Sale::active()->count() ?? 0,
                'total_customers' => Customer::active()->count() ?? 0,
                'total_products' => Product::active()->count() ?? 0,
                'total_revenue' => Sale::active()->sum('total_amount') ?? 0,
                'pending_orders' => Order::active()->where('order_status', 'pending')->count() ?? 0,
                'low_stock_products' => $this->getLowStockProducts(),
                'recent_orders' => $this->getRecentOrders(),
            ];

            // Get monthly sales data for chart
            $monthlySales = Sale::active()
                ->selectRaw('DATE_FORMAT(sale_date, "%Y-%m") as month, SUM(total_amount) as total')
                ->where('sale_date', '>=', now()->subMonths(12))
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            $recentLogins = LoginActivity::with('user')
                ->orderByDesc('created_at')
                ->limit(20)
                ->get();

            return view('dashboard', compact('stats', 'monthlySales', 'recentLogins'));
        } catch (\Exception $e) {
            // Fallback to basic stats if there are any issues
            $stats = [
                'total_orders' => 0,
                'total_sales' => 0,
                'total_customers' => 0,
                'total_products' => 0,
                'total_revenue' => 0,
                'pending_orders' => 0,
                'low_stock_products' => collect(),
                'recent_orders' => collect(),
            ];
            
            $monthlySales = collect();
            
            $recentLogins = collect();
            return view('dashboard', compact('stats', 'monthlySales', 'recentLogins'));
        }
    }

    private function getLowStockProducts()
    {
        try {
            return Inventory::with(['product', 'department'])
                ->whereColumn('quantity', '<=', 'reorder_point')
                ->where('quantity', '>', 0)
                ->get();
        } catch (\Exception $e) {
            return collect();
        }
    }

    private function getRecentOrders()
    {
        try {
            return Order::with(['customer', 'items.product'])
                ->active()
                ->latest()
                ->limit(5)
                ->get();
        } catch (\Exception $e) {
            return collect();
        }
    }
}

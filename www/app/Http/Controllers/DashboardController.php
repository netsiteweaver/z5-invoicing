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
    public function index(Request $request)
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

            // Placeholder for legacy variable used by the view
            $monthlySales = collect();

            // Sales chart filters
            $view = $request->input('sales_view', 'month'); // month|year|custom
            $year = (int) $request->input('sales_year', now()->year);
            $startInput = $request->input('sales_start');
            $endInput = $request->input('sales_end');

            if ($view === 'month') {
                $startDate = now()->copy()->startOfMonth();
                $endDate = now()->copy()->endOfDay();
                $groupDaily = true;
            } elseif ($view === 'year') {
                $startDate = now()->setYear($year)->startOfYear();
                $endDate = now()->setYear($year)->endOfYear();
                $groupDaily = false;
            } else { // custom
                $startDate = $startInput ? \Carbon\Carbon::parse($startInput)->startOfDay() : now()->subMonths(1)->startOfDay();
                $endDate = $endInput ? \Carbon\Carbon::parse($endInput)->endOfDay() : now()->endOfDay();
                $groupDaily = $startDate->diffInDays($endDate) <= 62;
            }

            if ($groupDaily) {
                $raw = Sale::active()
                    ->whereBetween('sale_date', [$startDate->toDateString(), $endDate->toDateString()])
                    ->selectRaw('DATE(sale_date) as period, SUM(total_amount) as total')
                    ->groupBy('period')
                    ->orderBy('period')
                    ->get();
                $labels = $raw->pluck('period')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d M'))->toArray();
                $data = $raw->pluck('total')->map(fn($v) => (float) $v)->toArray();
            } else {
                $raw = Sale::active()
                    ->whereBetween('sale_date', [$startDate->toDateString(), $endDate->toDateString()])
                    ->selectRaw('DATE_FORMAT(sale_date, "%Y-%m") as period, SUM(total_amount) as total')
                    ->groupBy('period')
                    ->orderBy('period')
                    ->get();
                $labels = $raw->pluck('period')->map(fn($m) => \Carbon\Carbon::parse($m . '-01')->format('M Y'))->toArray();
                $data = $raw->pluck('total')->map(fn($v) => (float) $v)->toArray();
            }

            $salesChart = [
                'labels' => $labels,
                'data' => $data,
                'filters' => [
                    'view' => $view,
                    'year' => $year,
                    'start' => optional($startDate)->toDateString(),
                    'end' => optional($endDate)->toDateString(),
                ],
            ];

            // Recent logins (robust via join to ensure names show regardless of relation)
            $recentLogins = DB::table('login_activities as la')
                ->leftJoin('users as u', 'u.id', '=', 'la.user_id')
                ->orderByDesc('la.created_at')
                ->limit(20)
                ->select('la.*', 'u.name as user_name')
                ->get();
                
            return view('dashboard', compact('stats', 'monthlySales', 'recentLogins', 'salesChart'));
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
            $salesChart = ['labels' => [], 'data' => [], 'filters' => ['view' => 'month', 'year' => now()->year, 'start' => now()->startOfMonth()->toDateString(), 'end' => now()->toDateString()]];
            return view('dashboard', compact('stats', 'monthlySales', 'recentLogins', 'salesChart'));
        }
    }

    private function getLowStockProducts()
    {
        try {
            return Inventory::with(['product', 'department'])
                ->whereColumn('current_stock', '<=', 'reorder_point')
                ->where('current_stock', '>', 0)
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

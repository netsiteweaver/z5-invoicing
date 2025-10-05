<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\Sale;
use App\Models\GoodsReceipt;
use App\Models\StockTransfer;
use App\Models\Inventory;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\PaymentType;
use App\Models\Supplier;

class ReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->middleware('permission:reports.view')->only(['index']);
        $this->middleware('permission:reports.orders')->only(['orders']);
        $this->middleware('permission:reports.sales')->only(['sales']);
        $this->middleware('permission:reports.goods_receipts')->only(['goodsReceipts']);
        $this->middleware('permission:reports.stock_transfers')->only(['stockTransfers']);
        $this->middleware('permission:reports.inventory')->only(['inventory']);
        $this->middleware('permission:reports.customers')->only(['customers']);
        $this->middleware('permission:reports.payments')->only(['payments']);
        $this->middleware('permission:reports.suppliers')->only(['suppliers']);
        $this->middleware('permission:reports.monthly_summary')->only(['monthlySummary']);
        $this->middleware('permission:reports.growth_analysis')->only(['growthAnalysis']);
        $this->middleware('permission:reports.alerts_warnings')->only(['alertsWarnings']);
    }

    public function index()
    {
        return view('reports.index');
    }

    public function orders(Request $request)
    {
        $query = Order::with(['customer', 'items.product']);

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // State filter
        if ($request->filled('state')) {
            $query->where('order_status', $request->state);
        }

        // Customer filter
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(50);
        $customers = Customer::ordered()->get();
        $states = ['draft', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'];

        return view('reports.orders', compact('orders', 'customers', 'states'));
    }

    public function sales(Request $request)
    {
        $query = Sale::with(['customer', 'saleItems.product']);

        // Period filter
        if ($request->filled('period')) {
            switch ($request->period) {
                case 'this_month':
                    $query->whereMonth('created_at', Carbon::now()->month)
                          ->whereYear('created_at', Carbon::now()->year);
                    break;
                case 'last_month':
                    $query->whereMonth('created_at', Carbon::now()->subMonth()->month)
                          ->whereYear('created_at', Carbon::now()->subMonth()->year);
                    break;
                case 'this_year':
                    $query->whereYear('created_at', Carbon::now()->year);
                    break;
                case 'last_year':
                    $query->whereYear('created_at', Carbon::now()->subYear()->year);
                    break;
                case 'custom':
                    if ($request->filled('date_from')) {
                        $query->whereDate('created_at', '>=', $request->date_from);
                    }
                    if ($request->filled('date_to')) {
                        $query->whereDate('created_at', '<=', $request->date_to);
                    }
                    break;
            }
        }

        $sales = $query->orderBy('created_at', 'desc')->paginate(50);

        // Best seller analysis
        $bestSellers = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->select('products.name', 'products.stockref as sku', DB::raw('SUM(sale_items.quantity) as total_quantity'), DB::raw('SUM(sale_items.line_total) as total_revenue'))
            ->when($request->filled('period'), function($query) use ($request) {
                switch ($request->period) {
                    case 'this_month':
                        return $query->whereMonth('sale_items.created_at', Carbon::now()->month)
                                   ->whereYear('sale_items.created_at', Carbon::now()->year);
                    case 'last_month':
                        return $query->whereMonth('sale_items.created_at', Carbon::now()->subMonth()->month)
                                   ->whereYear('sale_items.created_at', Carbon::now()->subMonth()->year);
                    case 'this_year':
                        return $query->whereYear('sale_items.created_at', Carbon::now()->year);
                    case 'last_year':
                        return $query->whereYear('sale_items.created_at', Carbon::now()->subYear()->year);
                    case 'custom':
                        if ($request->filled('date_from')) {
                            $query->whereDate('sale_items.created_at', '>=', $request->date_from);
                        }
                        if ($request->filled('date_to')) {
                            $query->whereDate('sale_items.created_at', '<=', $request->date_to);
                        }
                        return $query;
                }
            })
            ->groupBy('products.id', 'products.name', 'products.stockref')
            ->orderBy('total_quantity', 'desc')
            ->limit(10)
            ->get();

        // Sales by brand analysis
        $salesByBrand = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('product_brands', 'products.brand_id', '=', 'product_brands.id')
            ->select('product_brands.name as brand_name', DB::raw('SUM(sale_items.quantity) as total_quantity'), DB::raw('SUM(sale_items.line_total) as total_revenue'))
            ->when($request->filled('period'), function($query) use ($request) {
                switch ($request->period) {
                    case 'this_month':
                        return $query->whereMonth('sale_items.created_at', Carbon::now()->month)
                                   ->whereYear('sale_items.created_at', Carbon::now()->year);
                    case 'last_month':
                        return $query->whereMonth('sale_items.created_at', Carbon::now()->subMonth()->month)
                                   ->whereYear('sale_items.created_at', Carbon::now()->subMonth()->year);
                    case 'this_year':
                        return $query->whereYear('sale_items.created_at', Carbon::now()->year);
                    case 'last_year':
                        return $query->whereYear('sale_items.created_at', Carbon::now()->subYear()->year);
                    case 'custom':
                        if ($request->filled('date_from')) {
                            $query->whereDate('sale_items.created_at', '>=', $request->date_from);
                        }
                        if ($request->filled('date_to')) {
                            $query->whereDate('sale_items.created_at', '<=', $request->date_to);
                        }
                        return $query;
                }
            })
            ->groupBy('product_brands.id', 'product_brands.name')
            ->orderBy('total_revenue', 'desc')
            ->get();

        // Sales by category analysis
        $salesByCategory = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('product_categories', 'products.category_id', '=', 'product_categories.id')
            ->select('product_categories.name as category_name', DB::raw('SUM(sale_items.quantity) as total_quantity'), DB::raw('SUM(sale_items.line_total) as total_revenue'))
            ->when($request->filled('period'), function($query) use ($request) {
                switch ($request->period) {
                    case 'this_month':
                        return $query->whereMonth('sale_items.created_at', Carbon::now()->month)
                                   ->whereYear('sale_items.created_at', Carbon::now()->year);
                    case 'last_month':
                        return $query->whereMonth('sale_items.created_at', Carbon::now()->subMonth()->month)
                                   ->whereYear('sale_items.created_at', Carbon::now()->subMonth()->year);
                    case 'this_year':
                        return $query->whereYear('sale_items.created_at', Carbon::now()->year);
                    case 'last_year':
                        return $query->whereYear('sale_items.created_at', Carbon::now()->subYear()->year);
                    case 'custom':
                        if ($request->filled('date_from')) {
                            $query->whereDate('sale_items.created_at', '>=', $request->date_from);
                        }
                        if ($request->filled('date_to')) {
                            $query->whereDate('sale_items.created_at', '<=', $request->date_to);
                        }
                        return $query;
                }
            })
            ->groupBy('product_categories.id', 'product_categories.name')
            ->orderBy('total_revenue', 'desc')
            ->get();

        // Sales trend dataset (daily)
        $salesTrend = DB::table('sales')
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total_amount, COUNT(*) as sales_count')
            ->when($request->filled('period'), function($query) use ($request) {
                switch ($request->period) {
                    case 'this_month':
                        return $query->whereMonth('created_at', Carbon::now()->month)
                                     ->whereYear('created_at', Carbon::now()->year);
                    case 'last_month':
                        return $query->whereMonth('created_at', Carbon::now()->subMonth()->month)
                                     ->whereYear('created_at', Carbon::now()->subMonth()->year);
                    case 'this_year':
                        return $query->whereYear('created_at', Carbon::now()->year);
                    case 'last_year':
                        return $query->whereYear('created_at', Carbon::now()->subYear()->year);
                    case 'custom':
                        if ($request->filled('date_from')) {
                            $query->whereDate('created_at', '>=', $request->date_from);
                        }
                        if ($request->filled('date_to')) {
                            $query->whereDate('created_at', '<=', $request->date_to);
                        }
                        return $query;
                }
            })
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Pre-format trend data for the view to avoid Blade parser issues
        $salesTrendFormatted = $salesTrend->map(function ($row) {
            return [
                'date' => Carbon::parse($row->date)->format('M d, Y'),
                'amount' => (float) $row->total_amount,
                'count' => (int) $row->sales_count,
            ];
        })->values();

        $periods = [
            'this_month' => 'This Month',
            'last_month' => 'Last Month',
            'this_year' => 'This Year',
            'last_year' => 'Last Year',
            'custom' => 'Custom Range'
        ];

        return view('reports.sales', compact('sales', 'bestSellers', 'salesByBrand', 'salesByCategory', 'salesTrend', 'salesTrendFormatted', 'periods'));
    }

    public function goodsReceipts(Request $request)
    {
        $query = GoodsReceipt::with(['supplier', 'items.product']);

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Supplier filter
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $goodsReceipts = $query->orderBy('created_at', 'desc')->paginate(50);
        $suppliers = Supplier::orderBy('name')->get();
        $statuses = ['pending', 'approved', 'rejected'];

        return view('reports.goods-receipts', compact('goodsReceipts', 'suppliers', 'statuses'));
    }

    public function stockTransfers(Request $request)
    {
        $query = StockTransfer::with(['items.product']);

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $stockTransfers = $query->orderBy('created_at', 'desc')->paginate(50);
        $statuses = ['pending', 'in_transit', 'received', 'cancelled'];

        return view('reports.stock-transfers', compact('stockTransfers', 'statuses'));
    }

    public function inventory(Request $request)
    {
        $query = Inventory::with(['product']);

        // Product filter
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Category filter
        if ($request->filled('category_id')) {
            $query->whereHas('product', function($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }

        // Low stock filter
        if ($request->filled('low_stock')) {
            $query->whereColumn('current_stock', '<=', 'min_stock_level');
        }

        // Order by product name via subquery to avoid breaking eager loading
        $inventory = $query
            ->orderBy(
                \App\Models\Product::select('name')
                    ->whereColumn('products.id', 'inventory.product_id')
                    ->limit(1)
            )
            ->paginate(50);

        // Stock movement analysis
        $stockMovements = DB::table('stock_movements')
            ->join('products', 'stock_movements.product_id', '=', 'products.id')
            ->select('products.name', 'products.stockref as sku', 
                    DB::raw('SUM(CASE WHEN movement_type = "in" THEN quantity ELSE 0 END) as total_in'),
                    DB::raw('SUM(CASE WHEN movement_type = "out" THEN quantity ELSE 0 END) as total_out'))
            ->when($request->filled('date_from'), function($query) use ($request) {
                return $query->whereDate('stock_movements.movement_date', '>=', $request->date_from);
            })
            ->when($request->filled('date_to'), function($query) use ($request) {
                return $query->whereDate('stock_movements.movement_date', '<=', $request->date_to);
            })
            ->groupBy('products.id', 'products.name', 'products.stockref')
            ->orderBy('total_in', 'desc')
            ->get();

        return view('reports.inventory', compact('inventory', 'stockMovements'));
    }

    public function customers(Request $request)
    {
        $query = Customer::withCount(['orders', 'sales']);

        // Date range filter for customer activity
        if ($request->filled('date_from')) {
            $query->whereHas('orders', function($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->date_from);
            });
        }

        $customers = $query->orderBy('company_name')->paginate(50);

        // Top customers by revenue
        $topCustomers = DB::table('sales')
            ->join('customers', 'sales.customer_id', '=', 'customers.id')
            ->select(DB::raw("COALESCE(NULLIF(customers.company_name,''), customers.full_name) as name"), 'customers.email', 
                    DB::raw('COUNT(sales.id) as total_orders'),
                    DB::raw('SUM(sales.total_amount) as total_revenue'))
            ->when($request->filled('date_from'), function($query) use ($request) {
                return $query->whereDate('sales.created_at', '>=', $request->date_from);
            })
            ->when($request->filled('date_to'), function($query) use ($request) {
                return $query->whereDate('sales.created_at', '<=', $request->date_to);
            })
            ->groupBy('customers.id', 'customers.company_name', 'customers.full_name', 'customers.email')
            ->orderBy('total_revenue', 'desc')
            ->limit(10)
            ->get();

        return view('reports.customers', compact('customers', 'topCustomers'));
    }

    public function payments(Request $request)
    {
        $query = Payment::with(['sale.customer']);

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Payment method filter
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(50);

        // Outstanding payments
        $outstandingPayments = DB::table('sales')
            ->join('customers', 'sales.customer_id', '=', 'customers.id')
            ->select(DB::raw("COALESCE(NULLIF(customers.company_name,''), customers.full_name) as name"), 'sales.id as sale_id', 'sales.total_amount', 
                    DB::raw('COALESCE(SUM(payments.amount), 0) as paid_amount'),
                    DB::raw('sales.total_amount - COALESCE(SUM(payments.amount), 0) as outstanding_amount'))
            ->leftJoin('payments', 'sales.id', '=', 'payments.sale_id')
            ->groupBy('sales.id', 'customers.company_name', 'customers.full_name', 'sales.total_amount')
            ->having('outstanding_amount', '>', 0)
            ->orderBy('outstanding_amount', 'desc')
            ->get();

        $paymentTypes = PaymentType::active()->ordered()->get();

        return view('reports.payments', compact('payments', 'outstandingPayments', 'paymentTypes'));
    }

    public function suppliers(Request $request)
    {
        $query = Supplier::withCount(['goodsReceipts']);

        // Date range filter for supplier activity
        if ($request->filled('date_from')) {
            $query->whereHas('goodsReceipts', function($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->date_from);
            });
        }

        $suppliers = $query->orderBy('name')->paginate(50);

        // Supplier performance analysis
        $supplierPerformance = DB::table('goods_receipts')
            ->join('suppliers', 'goods_receipts.supplier_id', '=', 'suppliers.id')
            ->join('goods_receipt_items', 'goods_receipts.id', '=', 'goods_receipt_items.goods_receipt_id')
            ->select('suppliers.name', 'suppliers.email',
                    DB::raw('COUNT(goods_receipts.id) as total_receipts'),
                    DB::raw('SUM(goods_receipt_items.quantity * COALESCE(goods_receipt_items.unit_cost, 0)) as total_purchases'),
                    DB::raw('AVG(goods_receipt_items.quantity * COALESCE(goods_receipt_items.unit_cost, 0)) as avg_purchase_value'))
            ->when($request->filled('date_from'), function($query) use ($request) {
                return $query->whereDate('goods_receipts.created_at', '>=', $request->date_from);
            })
            ->when($request->filled('date_to'), function($query) use ($request) {
                return $query->whereDate('goods_receipts.created_at', '<=', $request->date_to);
            })
            ->groupBy('suppliers.id', 'suppliers.name', 'suppliers.email')
            ->orderBy('total_purchases', 'desc')
            ->get();

        return view('reports.suppliers', compact('suppliers', 'supplierPerformance'));
    }

    public function monthlySummary(Request $request)
    {
        // Default to current month if no date specified
        $month = $request->get('month', now()->format('Y-m'));
        $startDate = Carbon::parse($month . '-01')->startOfMonth();
        $endDate = Carbon::parse($month . '-01')->endOfMonth();

        // Monthly sales data
        $monthlySales = Sale::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as daily_total, COUNT(*) as daily_count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Monthly orders data
        $monthlyOrders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as daily_count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top products for the month
        $topProducts = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->selectRaw('products.name, SUM(sale_items.quantity) as total_quantity, SUM(sale_items.line_total) as total_amount')
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_quantity', 'desc')
            ->limit(10)
            ->get();

        // Monthly totals
        $totalSales = Sale::whereBetween('created_at', [$startDate, $endDate])->sum('total_amount');
        $totalOrders = Order::whereBetween('created_at', [$startDate, $endDate])->count();
        $totalCustomers = Customer::whereBetween('created_at', [$startDate, $endDate])->count();

        return view('reports.monthly-summary', compact(
            'month', 'monthlySales', 'monthlyOrders', 'topProducts', 
            'totalSales', 'totalOrders', 'totalCustomers', 'startDate', 'endDate'
        ));
    }

    public function growthAnalysis(Request $request)
    {
        // Default to current year
        $year = $request->get('year', now()->year);
        $previousYear = $year - 1;

        // Monthly sales comparison
        $currentYearSales = Sale::whereYear('created_at', $year)
            ->selectRaw('MONTH(created_at) as month, SUM(total_amount) as total, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $previousYearSales = Sale::whereYear('created_at', $previousYear)
            ->selectRaw('MONTH(created_at) as month, SUM(total_amount) as total, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        // Year-over-year totals
        $currentYearTotal = Sale::whereYear('created_at', $year)->sum('total_amount');
        $previousYearTotal = Sale::whereYear('created_at', $previousYear)->sum('total_amount');
        $salesGrowth = $previousYearTotal > 0 ? (($currentYearTotal - $previousYearTotal) / $previousYearTotal) * 100 : 0;

        $currentYearOrders = Order::whereYear('created_at', $year)->count();
        $previousYearOrders = Order::whereYear('created_at', $previousYear)->count();
        $ordersGrowth = $previousYearOrders > 0 ? (($currentYearOrders - $previousYearOrders) / $previousYearOrders) * 100 : 0;

        // Customer growth
        $currentYearCustomers = Customer::whereYear('created_at', $year)->count();
        $previousYearCustomers = Customer::whereYear('created_at', $previousYear)->count();
        $customersGrowth = $previousYearCustomers > 0 ? (($currentYearCustomers - $previousYearCustomers) / $previousYearCustomers) * 100 : 0;

        // Top performing months
        $topMonths = collect();
        for ($i = 1; $i <= 12; $i++) {
            $current = $currentYearSales->get($i);
            $previous = $previousYearSales->get($i);
            
            if ($current && $previous) {
                $growth = $previous->total > 0 ? (($current->total - $previous->total) / $previous->total) * 100 : 0;
                $topMonths->push([
                    'month' => $i,
                    'month_name' => Carbon::create()->month($i)->format('F'),
                    'current_total' => $current->total,
                    'previous_total' => $previous->total,
                    'growth' => $growth
                ]);
            }
        }

        return view('reports.growth-analysis', compact(
            'year', 'previousYear', 'currentYearSales', 'previousYearSales',
            'currentYearTotal', 'previousYearTotal', 'salesGrowth',
            'currentYearOrders', 'previousYearOrders', 'ordersGrowth',
            'currentYearCustomers', 'previousYearCustomers', 'customersGrowth',
            'topMonths'
        ));
    }

    public function alertsWarnings(Request $request)
    {
        $alerts = collect();

        // Low stock alerts
        $lowStockItems = Inventory::whereColumn('current_stock', '<=', 'min_stock_level')->get();
        foreach ($lowStockItems as $item) {
            $alerts->push([
                'type' => 'warning',
                'title' => 'Low Stock Alert',
                'message' => "Product '{$item->product->name}' is running low. Current stock: {$item->current_stock}, Minimum required: {$item->min_stock_level}",
                'severity' => $item->current_stock == 0 ? 'critical' : 'warning',
                'date' => now(),
                'icon' => 'fas fa-exclamation-triangle'
            ]);
        }

        // Overdue payments
        $overduePayments = Payment::where('status', 'pending')
            ->where('due_date', '<', now())
            ->with('customer')
            ->get();

        foreach ($overduePayments as $payment) {
            $alerts->push([
                'type' => 'warning',
                'title' => 'Overdue Payment',
                'message' => "Payment of {$payment->amount} from {$payment->customer->name} is overdue since {$payment->due_date->format('M d, Y')}",
                'severity' => 'warning',
                'date' => $payment->due_date,
                'icon' => 'fas fa-money-bill-wave'
            ]);
        }

        // Pending orders older than 7 days
        $oldPendingOrders = Order::whereIn('order_status', ['draft', 'confirmed'])
            ->where('created_at', '<', now()->subDays(7))
            ->with('customer')
            ->get();

        foreach ($oldPendingOrders as $order) {
            $alerts->push([
                'type' => 'info',
                'title' => 'Old Pending Order',
                'message' => "Order #{$order->id} from {$order->customer->name} has been pending for more than 7 days",
                'severity' => 'info',
                'date' => $order->created_at,
                'icon' => 'fas fa-clock'
            ]);
        }

        // High-value orders requiring attention
        $highValueOrders = Order::where('total_amount', '>', 10000)
            ->whereIn('order_status', ['draft', 'confirmed'])
            ->with('customer')
            ->get();

        foreach ($highValueOrders as $order) {
            $alerts->push([
                'type' => 'info',
                'title' => 'High Value Order',
                'message' => "Order #{$order->id} from {$order->customer->name} has a high value of {$order->total_amount}",
                'severity' => 'info',
                'date' => $order->created_at,
                'icon' => 'fas fa-star'
            ]);
        }

        // Sort alerts by severity and date
        $alerts = $alerts->sortByDesc(function ($alert) {
            $severityOrder = ['critical' => 3, 'warning' => 2, 'info' => 1];
            return $severityOrder[$alert['severity']] * 1000 + $alert['date']->timestamp;
        });

        return view('reports.alerts-warnings', compact('alerts'));
    }
}
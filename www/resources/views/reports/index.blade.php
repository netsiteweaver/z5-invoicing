@extends('layouts.app')

@section('title', 'Reports')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Reports Dashboard</h2>
            <p class="mt-1 text-sm text-gray-600">Access comprehensive reports and analytics for your business operations.</p>
        </div>
    </div>

    <!-- Report Categories -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Sales & Orders Reports -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-shopping-cart text-blue-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Sales & Orders</h3>
                    <p class="text-sm text-gray-500">Track sales performance and order analytics</p>
                </div>
            </div>
            <div class="mt-4 space-y-2">
                <a href="{{ route('reports.orders') }}" class="block p-3 rounded-md hover:bg-gray-50 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-file-alt text-gray-400 mr-3"></i>
                            <span class="text-sm font-medium text-gray-900">Orders Report</span>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Date range, state, and customer filters</p>
                </a>
                <a href="{{ route('reports.sales') }}" class="block p-3 rounded-md hover:bg-gray-50 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-chart-line text-gray-400 mr-3"></i>
                            <span class="text-sm font-medium text-gray-900">Sales Report</span>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Period analysis and best sellers</p>
                </a>
            </div>
        </div>

        <!-- Inventory Reports -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-warehouse text-green-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Inventory & Stock</h3>
                    <p class="text-sm text-gray-500">Monitor stock levels and movements</p>
                </div>
            </div>
            <div class="mt-4 space-y-2">
                <a href="{{ route('reports.inventory') }}" class="block p-3 rounded-md hover:bg-gray-50 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-boxes text-gray-400 mr-3"></i>
                            <span class="text-sm font-medium text-gray-900">Inventory Report</span>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Stock levels and movement analysis</p>
                </a>
                <a href="{{ route('reports.goods-receipts') }}" class="block p-3 rounded-md hover:bg-gray-50 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-truck-loading text-gray-400 mr-3"></i>
                            <span class="text-sm font-medium text-gray-900">Goods Receipts</span>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Incoming stock and supplier analysis</p>
                </a>
                <a href="{{ route('reports.stock-transfers') }}" class="block p-3 rounded-md hover:bg-gray-50 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-exchange-alt text-gray-400 mr-3"></i>
                            <span class="text-sm font-medium text-gray-900">Stock Transfers</span>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Internal stock movement tracking</p>
                </a>
            </div>
        </div>

        <!-- Customer & Payment Reports -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-purple-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Customers & Payments</h3>
                    <p class="text-sm text-gray-500">Customer insights and payment tracking</p>
                </div>
            </div>
            <div class="mt-4 space-y-2">
                <a href="{{ route('reports.customers') }}" class="block p-3 rounded-md hover:bg-gray-50 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-user-friends text-gray-400 mr-3"></i>
                            <span class="text-sm font-medium text-gray-900">Customer Analysis</span>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Customer performance and top buyers</p>
                </a>
                <a href="{{ route('reports.payments') }}" class="block p-3 rounded-md hover:bg-gray-50 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-money-bill-wave text-gray-400 mr-3"></i>
                            <span class="text-sm font-medium text-gray-900">Payment Analysis</span>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Payment tracking and outstanding amounts</p>
                </a>
            </div>
        </div>

        <!-- Supplier Reports -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-truck text-orange-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Suppliers</h3>
                    <p class="text-sm text-gray-500">Supplier performance and purchase analysis</p>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('reports.suppliers') }}" class="block p-3 rounded-md hover:bg-gray-50 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-chart-pie text-gray-400 mr-3"></i>
                            <span class="text-sm font-medium text-gray-900">Supplier Analysis</span>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Supplier performance and purchase trends</p>
                </a>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-bar text-indigo-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Quick Stats</h3>
                    <p class="text-sm text-gray-500">Key performance indicators</p>
                </div>
            </div>
            <div class="mt-4 space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Total Orders Today</span>
                    <span class="text-lg font-semibold text-gray-900">{{ \App\Models\Order::whereDate('created_at', today())->count() }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Total Sales Today</span>
                    <span class="text-lg font-semibold text-gray-900">${{ number_format(\App\Models\Sale::whereDate('created_at', today())->sum('total_amount'), 2) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Low Stock Items</span>
                    <span class="text-lg font-semibold text-red-600">{{ \App\Models\Inventory::whereColumn('current_stock', '<=', 'min_stock_level')->count() }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Pending Orders</span>
                    <span class="text-lg font-semibold text-yellow-600">{{ \App\Models\Order::whereIn('order_status', ['draft', 'confirmed', 'processing'])->count() }}</span>
                </div>
            </div>
        </div>

        <!-- Additional Reports -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-teal-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-file-alt text-teal-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Additional Reports</h3>
                    <p class="text-sm text-gray-500">Specialized business reports</p>
                </div>
            </div>
            <div class="mt-4 space-y-2">
                <div class="p-3 rounded-md bg-gray-50 border border-gray-200">
                    <div class="flex items-center">
                        <i class="fas fa-calendar-alt text-gray-400 mr-3"></i>
                        <span class="text-sm font-medium text-gray-900">Monthly Summary</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Comprehensive monthly business overview</p>
                </div>
                <div class="p-3 rounded-md bg-gray-50 border border-gray-200">
                    <div class="flex items-center">
                        <i class="fas fa-trending-up text-gray-400 mr-3"></i>
                        <span class="text-sm font-medium text-gray-900">Growth Analysis</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Year-over-year growth metrics</p>
                </div>
                <div class="p-3 rounded-md bg-gray-50 border border-gray-200">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-gray-400 mr-3"></i>
                        <span class="text-sm font-medium text-gray-900">Alerts & Warnings</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">System alerts and recommendations</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Recent Activity</h3>
        </div>
        <div class="px-6 py-4">
            <div class="flow-root">
                <ul class="-mb-8">
                    @php
                        $recentOrders = \App\Models\Order::with('customer')->latest()->limit(5)->get();
                        $recentSales = \App\Models\Sale::with('customer')->latest()->limit(5)->get();
                        $recentActivities = collect();
                        
                        foreach($recentOrders as $order) {
                            $recentActivities->push([
                                'type' => 'order',
                                'title' => 'New Order #' . $order->id,
                                'description' => 'Order from ' . $order->customer->name,
                                'time' => $order->created_at,
                                'icon' => 'fas fa-shopping-cart',
                                'color' => 'text-blue-500'
                            ]);
                        }
                        
                        foreach($recentSales as $sale) {
                            $recentActivities->push([
                                'type' => 'sale',
                                'title' => 'Sale #' . $sale->id,
                                'description' => 'Sale to ' . $sale->customer->name,
                                'time' => $sale->created_at,
                                'icon' => 'fas fa-chart-line',
                                'color' => 'text-green-500'
                            ]);
                        }
                        
                        $recentActivities = $recentActivities->sortByDesc('time')->take(10);
                    @endphp
                    
                    @forelse($recentActivities as $activity)
                    <li class="relative pb-8">
                        <div class="relative flex space-x-3">
                            <div>
                                <span class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center ring-8 ring-white">
                                    <i class="{{ $activity['icon'] }} {{ $activity['color'] }} text-xs"></i>
                                </span>
                            </div>
                            <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                <div>
                                    <p class="text-sm text-gray-500">{{ $activity['title'] }}</p>
                                    <p class="text-xs text-gray-400">{{ $activity['description'] }}</p>
                                </div>
                                <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                    {{ $activity['time']->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    </li>
                    @empty
                    <li class="relative">
                        <div class="text-center py-4">
                            <i class="fas fa-info-circle text-gray-400 text-2xl mb-2"></i>
                            <p class="text-sm text-gray-500">No recent activity to display</p>
                        </div>
                    </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
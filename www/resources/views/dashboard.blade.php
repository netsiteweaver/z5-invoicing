@extends('layouts.app')

@section('title', 'Dashboard')
@section('description', 'Overview of your business metrics and recent activity')

@section('content')
<div id="vue-dashboard-stagger" v-cloak x-ignore>
<!-- Info Boxes -->
<div class="grid grid-cols-1 gap-4 sm:gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-6">
    <!-- Total Orders -->
    <div class="bg-white overflow-hidden shadow rounded-lg transform transition duration-300 ease-out" :class="show.infoBoxes ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-2'">
        <div class="p-4 sm:p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-blue-500 rounded-md flex items-center justify-center">
                        <i class="fas fa-shopping-cart text-white text-sm sm:text-base"></i>
                    </div>
                </div>
                <div class="ml-3 sm:ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-xs sm:text-sm font-medium text-gray-500 truncate">Total Orders</dt>
                        <dd class="text-lg sm:text-xl font-medium text-gray-900">{{ $stats['total_orders'] ?? 0 }}</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-4 py-3 sm:px-5">
            <div class="text-xs sm:text-sm">
                <a href="{{ route('orders.index') }}" class="font-medium text-blue-600 hover:text-blue-500">
                    View all orders
                </a>
            </div>
        </div>
    </div>

    <!-- Total Sales -->
    <div class="bg-white overflow-hidden shadow rounded-lg transform transition duration-300 ease-out" :class="show.infoBoxes ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-2'">
        <div class="p-4 sm:p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-green-500 rounded-md flex items-center justify-center">
                        <i class="fas fa-chart-line text-white text-sm sm:text-base"></i>
                    </div>
                </div>
                <div class="ml-3 sm:ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-xs sm:text-sm font-medium text-gray-500 truncate">Total Sales</dt>
                        <dd class="text-lg sm:text-xl font-medium text-gray-900">{{ $stats['total_sales'] ?? 0 }}</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-4 py-3 sm:px-5">
            <div class="text-xs sm:text-sm">
                <a href="{{ route('sales.index') }}" class="font-medium text-green-600 hover:text-green-500">
                    View sales report
                </a>
            </div>
        </div>
    </div>

    <!-- Total Customers -->
    <div class="bg-white overflow-hidden shadow rounded-lg transform transition duration-300 ease-out" :class="show.infoBoxes ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-2'">
        <div class="p-4 sm:p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-yellow-500 rounded-md flex items-center justify-center">
                        <i class="fas fa-users text-white text-sm sm:text-base"></i>
                    </div>
                </div>
                <div class="ml-3 sm:ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-xs sm:text-sm font-medium text-gray-500 truncate">Total Customers</dt>
                        <dd class="text-lg sm:text-xl font-medium text-gray-900">{{ $stats['total_customers'] ?? 0 }}</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-4 py-3 sm:px-5">
            <div class="text-xs sm:text-sm">
                <a href="{{ route('customers.index') }}" class="font-medium text-yellow-600 hover:text-yellow-500">
                    View all customers
                </a>
            </div>
        </div>
    </div>

    <!-- Total Products -->
    <div class="bg-white overflow-hidden shadow rounded-lg transform transition duration-300 ease-out" :class="show.infoBoxes ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-2'">
        <div class="p-4 sm:p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-red-500 rounded-md flex items-center justify-center">
                        <i class="fas fa-box text-white text-sm sm:text-base"></i>
                    </div>
                </div>
                <div class="ml-3 sm:ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-xs sm:text-sm font-medium text-gray-500 truncate">Total Products</dt>
                        <dd class="text-lg sm:text-xl font-medium text-gray-900">{{ $stats['total_products'] ?? 0 }}</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-4 py-3 sm:px-5">
            <div class="text-xs sm:text-sm">
                <a href="{{ route('products.index') }}" class="font-medium text-red-600 hover:text-red-500">
                    View all products
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Revenue and Pending Orders + Sales Trend -->
<div class="grid grid-cols-1 gap-5 lg:grid-cols-3 mb-6">
    <!-- Revenue Overview -->
    <div class="bg-white shadow rounded-lg lg:col-span-1 transform transition duration-300 ease-out" :class="show.revenue ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-2'">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                <i class="fas fa-dollar-sign mr-2"></i>
                Revenue Overview
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                <div class="text-center p-3 sm:p-4 bg-blue-50 rounded-lg">
                    <div class="text-xl sm:text-2xl font-bold text-blue-600">Rs {{ number_format($stats['total_revenue'] ?? 0, 2) }}</div>
                    <div class="text-xs sm:text-sm text-gray-500">Total Revenue</div>
                </div>
                <div class="text-center p-3 sm:p-4 bg-yellow-50 rounded-lg">
                    <div class="text-xl sm:text-2xl font-bold text-yellow-600">{{ $stats['pending_orders'] ?? 0 }}</div>
                    <div class="text-xs sm:text-sm text-gray-500">Pending Orders</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Trend -->
    <div class="bg-white shadow rounded-lg lg:col-span-2 transform transition duration-300 ease-out" :class="show.sales ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-2'">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 space-y-3 sm:space-y-0">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    <i class="fas fa-chart-area mr-2"></i>
                    Sales Trend
                </h3>
                <form method="GET" action="{{ route('dashboard') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center space-y-2 sm:space-y-0 sm:space-x-2 text-sm">
                    <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2">
                        <select name="sales_view" class="border-gray-300 rounded-md shadow-sm text-sm px-3 py-2" onchange="this.form.submit()">
                            <option value="month" {{ ($salesChart['filters']['view'] ?? 'month') === 'month' ? 'selected' : '' }}>Month</option>
                            <option value="year" {{ ($salesChart['filters']['view'] ?? 'month') === 'year' ? 'selected' : '' }}>Year</option>
                            <option value="custom" {{ ($salesChart['filters']['view'] ?? 'month') === 'custom' ? 'selected' : '' }}>Custom</option>
                        </select>
                        <input type="number" name="sales_year" min="2000" max="2100" value="{{ $salesChart['filters']['year'] ?? now()->year }}" class="border-gray-300 rounded-md shadow-sm text-sm px-3 py-2 {{ ($salesChart['filters']['view'] ?? 'month') === 'year' ? '' : 'hidden' }}" placeholder="Year" />
                    </div>
                    <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2 {{ ($salesChart['filters']['view'] ?? 'month') === 'custom' ? '' : 'hidden' }}" id="custom-date-inputs">
                        <input type="date" name="sales_start" value="{{ $salesChart['filters']['start'] ?? '' }}" class="border-gray-300 rounded-md shadow-sm text-sm px-3 py-2" placeholder="Start Date" />
                        <input type="date" name="sales_end" value="{{ $salesChart['filters']['end'] ?? '' }}" class="border-gray-300 rounded-md shadow-sm text-sm px-3 py-2" placeholder="End Date" />
                    </div>
                    <button class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors text-sm font-medium">
                        <i class="fas fa-filter mr-1"></i> Apply
                    </button>
                </form>
            </div>
            <div id="vue-dashboard-root" v-pre class="h-64 md:h-80" data-labels='@json($salesChart['labels'] ?? [])' data-series='@json($salesChart['data'] ?? [])'></div>
        </div>
    </div>
</div>

<!-- Low Stock Products -->
@if(isset($stats['low_stock_products']) && $stats['low_stock_products']->count() > 0)
<div class="bg-white shadow rounded-lg mb-6 transform transition duration-300 ease-out" :class="show.lowStock ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-2'">
    <div class="px-4 py-5 sm:p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>
                Low Stock Products
            </h3>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                {{ $stats['low_stock_products']->count() }} Items
            </span>
        </div>
        
        <!-- Mobile cards (visible on small screens) -->
        <div class="sm:hidden space-y-4">
            @foreach($stats['low_stock_products'] as $product)
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mr-3">
                            <span class="text-gray-600 font-medium text-sm">{{ substr($product->name, 0, 1) }}</span>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                            <div class="text-xs text-gray-500">{{ $product->sku }}</div>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        Low Stock
                    </span>
                </div>
                <div class="grid grid-cols-2 gap-3 mb-3">
                    <div>
                        <div class="text-xs text-gray-500 mb-1">Current Stock</div>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            {{ $product->current_stock }}
                        </span>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 mb-1">Min Stock</div>
                        <div class="text-sm font-medium text-gray-900">{{ $product->min_stock_level }}</div>
                    </div>
                </div>
                <div class="flex justify-end">
                    <a href="{{ route('products.show', $product) }}" class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-xs font-medium rounded-md hover:bg-blue-700 transition-colors">
                        <i class="fas fa-eye mr-1"></i> View Product
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- Desktop table (hidden on small screens) -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Min Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($stats['low_stock_products'] as $product)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-gray-600 font-medium text-sm">{{ substr($product->name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $product->sku }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                {{ $product->current_stock }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $product->min_stock_level }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Low Stock
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('products.show', $product) }}" class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-eye mr-1"></i> View
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<!-- Recent Orders -->
@if(isset($stats['recent_orders']) && $stats['recent_orders']->count() > 0)
<div class="bg-white shadow rounded-lg mb-6 transform transition duration-300 ease-out" :class="show.recentOrders ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-2'">
    <div class="px-4 py-5 sm:p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                <i class="fas fa-clock mr-2"></i>
                Recent Orders
            </h3>
            <a href="{{ route('orders.index') }}" class="text-sm text-blue-600 hover:text-blue-500">
                View all orders
            </a>
        </div>
        
        <!-- Mobile cards (visible on small screens) -->
        <div class="sm:hidden space-y-4">
            @foreach($stats['recent_orders'] as $order)
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center mr-3">
                            <span class="text-gray-600 font-medium text-xs">{{ substr($order->customer->display_name ?? 'N', 0, 1) }}</span>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ $order->order_number }}</div>
                            <div class="text-xs text-gray-500">{{ $order->customer->display_name ?? 'N/A' }}</div>
                        </div>
                    </div>
                    @if($order->order_status === 'draft')
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{{ ucfirst($order->order_status) }}</span>
                    @elseif($order->order_status === 'pending')
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">{{ ucfirst($order->order_status) }}</span>
                    @elseif($order->order_status === 'confirmed')
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{{ ucfirst($order->order_status) }}</span>
                    @elseif($order->order_status === 'processing')
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">{{ ucfirst($order->order_status) }}</span>
                    @elseif($order->order_status === 'shipped')
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">{{ ucfirst($order->order_status) }}</span>
                    @elseif($order->order_status === 'delivered')
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">{{ ucfirst($order->order_status) }}</span>
                    @elseif($order->order_status === 'cancelled')
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">{{ ucfirst($order->order_status) }}</span>
                    @endif
                </div>
                <div class="grid grid-cols-2 gap-3 mb-3">
                    <div>
                        <div class="text-xs text-gray-500 mb-1">Date</div>
                        <div class="text-sm font-medium text-gray-900">{{ $order->order_date ? \Carbon\Carbon::parse($order->order_date)->format('M d, Y') : 'N/A' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 mb-1">Total</div>
                        <div class="text-sm font-medium text-gray-900">Rs {{ number_format($order->total_amount, 2) }}</div>
                    </div>
                </div>
                <div class="flex justify-end">
                    <a href="{{ route('orders.show', $order) }}" class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-xs font-medium rounded-md hover:bg-blue-700 transition-colors">
                        <i class="fas fa-eye mr-1"></i> View Order
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- Desktop table (hidden on small screens) -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($stats['recent_orders'] as $order)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $order->order_number }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-gray-600 font-medium text-xs">{{ substr($order->customer->display_name ?? 'N', 0, 1) }}</span>
                                </div>
                                <div class="text-sm text-gray-900">{{ $order->customer->display_name ?? 'N/A' }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $order->order_date ? \Carbon\Carbon::parse($order->order_date)->format('M d, Y') : 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($order->order_status === 'draft')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{{ ucfirst($order->order_status) }}</span>
                            @elseif($order->order_status === 'pending')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">{{ ucfirst($order->order_status) }}</span>
                            @elseif($order->order_status === 'confirmed')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{{ ucfirst($order->order_status) }}</span>
                            @elseif($order->order_status === 'processing')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">{{ ucfirst($order->order_status) }}</span>
                            @elseif($order->order_status === 'shipped')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">{{ ucfirst($order->order_status) }}</span>
                            @elseif($order->order_status === 'delivered')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">{{ ucfirst($order->order_status) }}</span>
                            @elseif($order->order_status === 'cancelled')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">{{ ucfirst($order->order_status) }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">Rs {{ number_format($order->total_amount, 2) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('orders.show', $order) }}" class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-eye mr-1"></i> View
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<!-- Recent Logins -->
<div class="bg-white shadow rounded-lg mb-6 transform transition duration-300 ease-out" :class="show.recentLogins ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-2'">
    <div class="px-4 py-5 sm:p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                <i class="fas fa-sign-in-alt mr-2"></i>
                Recent Logins
            </h3>
        </div>
        @if(isset($recentLogins) && $recentLogins->count() > 0)
        
        <!-- Mobile cards (visible on small screens) -->
        <div class="sm:hidden space-y-4">
            @foreach($recentLogins as $log)
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-user text-gray-600 text-xs"></i>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ $log->user_name ?? $log->email ?? 'Unknown' }}</div>
                            <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($log->created_at)->format('M d, Y H:i') }}</div>
                        </div>
                    </div>
                    @if($log->status === 'success')
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Success</span>
                    @else
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">Failed</span>
                    @endif
                </div>
                <div class="grid grid-cols-1 gap-2 mb-3">
                    <div>
                        <div class="text-xs text-gray-500 mb-1">Device</div>
                        <div class="text-sm font-medium text-gray-900">{{ $log->device }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 mb-1">OS / Browser</div>
                        <div class="text-sm font-medium text-gray-900">{{ $log->os }} / {{ $log->browser }}</div>
                    </div>
                    @if(auth()->user()->is_root)
                    <div>
                        <div class="text-xs text-gray-500 mb-1">IP Address</div>
                        <div class="text-sm font-medium text-gray-900 font-mono">{{ $log->ip_address }}</div>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- Desktop table (hidden on small screens) -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">When</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        @if(auth()->user()->is_root)
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP</th>
                        @endif
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Device</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">OS / Browser</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($recentLogins as $log)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ \Carbon\Carbon::parse($log->created_at)->format('Y-m-d H:i') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $log->user_name ?? $log->email ?? 'Unknown' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($log->status === 'success')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Success</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Failed</span>
                            @endif
                        </td>
                        @if(auth()->user()->is_root)
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $log->ip_address }}</td>
                        @endif
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $log->device }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $log->os }} / {{ $log->browser }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        @else
        <div class="text-sm text-gray-500">No login activity yet.</div>
        @endif
    </div>
</div>
<!-- /vue-dashboard-stagger root -->
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle mobile form interactions for Sales Trend filters
    const salesViewSelect = document.querySelector('select[name="sales_view"]');
    const customDateInputs = document.getElementById('custom-date-inputs');
    
    if (salesViewSelect) {
        salesViewSelect.addEventListener('change', function() {
            const view = this.value;
            
            // Show/hide custom date inputs based on selection
            if (view === 'custom') {
                customDateInputs.classList.remove('hidden');
            } else {
                customDateInputs.classList.add('hidden');
            }
            
            // Auto-submit form on mobile for better UX
            if (window.innerWidth < 640) {
                this.form.submit();
            }
        });
    }
    
    // Initialize form state on page load
    if (salesViewSelect && customDateInputs) {
        const currentView = salesViewSelect.value;
        if (currentView === 'custom') {
            customDateInputs.classList.remove('hidden');
        } else {
            customDateInputs.classList.add('hidden');
        }
    }
});
</script>
@endpush
@extends('layouts.app')

@section('title', 'Orders Report')

@section('breadcrumbs')
<li>
    <div class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
        <a href="{{ route('reports.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">Reports</a>
    </div>
    </li>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-white shadow rounded-lg print:hidden no-print">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Orders Report</h2>
                    <p class="mt-1 text-sm text-gray-600">Analyze order data with date range, state, and customer filters.</p>
                </div>
                <div class="flex space-x-3">
                    <button onclick="window.print()" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <i class="fas fa-print mr-2"></i>
                        Print Report
                    </button>
                    <button onclick="exportToCSV()" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                        <i class="fas fa-download mr-2"></i>
                        Export CSV
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg print:hidden no-print" x-data="{ mobileOpen: false }">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
            <!-- Mobile toggle -->
            <div class="flex items-center justify-between sm:hidden">
                <h3 class="text-lg font-medium text-gray-900">Filters</h3>
                <button type="button" @click="mobileOpen = !mobileOpen" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-filter mr-2"></i>
                    <span x-show="!mobileOpen">Show</span>
                    <span x-show="mobileOpen">Hide</span>
                </button>
            </div>
            <!-- Desktop title -->
            <h3 class="hidden sm:block text-lg font-medium text-gray-900">Filters</h3>
        </div>
        <div class="px-4 sm:px-6 py-4">
            <div class="mt-4 sm:mt-0" x-cloak x-show="mobileOpen || window.matchMedia('(min-width: 640px)').matches">
                <form method="GET" action="{{ route('reports.orders') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label for="date_from" class="block text-sm font-medium text-gray-700">Date From</label>
                        <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="date_to" class="block text-sm font-medium text-gray-700">Date To</label>
                        <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-700">State</label>
                        <select name="state" id="state" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">All States</option>
                            @foreach($states as $state)
                            <option value="{{ $state }}" {{ request('state') == $state ? 'selected' : '' }}>
                                {{ ucfirst($state) }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="customer_id" class="block text-sm font-medium text-gray-700">Customer</label>
                        <select name="customer_id" id="customer_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">All Customers</option>
                            @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end space-x-3">
                        <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                            <i class="fas fa-search mr-2"></i>
                            Apply Filters
                        </button>
                        <a href="{{ route('reports.orders') }}" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fas fa-times mr-2"></i>
                            Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="report-cards">
        <div class="bg-white overflow-hidden shadow rounded-lg p-4 report-card">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-shopping-cart text-blue-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Orders</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $orders->total() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white overflow-hidden shadow rounded-lg p-4 report-card">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-dollar-sign text-green-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Value</dt>
                            <dd class="text-lg font-medium text-gray-900">${{ number_format($orders->sum('total_amount'), 2) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white overflow-hidden shadow rounded-lg p-4 report-card">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-clock text-yellow-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Pending Orders</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $orders->whereIn('order_status', ['draft', 'confirmed', 'processing'])->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white overflow-hidden shadow rounded-lg p-4 report-card">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Completed</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $orders->where('order_status', 'delivered')->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Orders</h3>
        </div>
        
        <!-- Desktop Table -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">State</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider no-print">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($orders as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $order->order_number ?? ('#'.$order->id) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $order->customer->display_name ?? $order->customer->company_name ?? $order->customer->full_name ?? '—' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $order->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($order->order_status == 'draft') bg-gray-100 text-gray-800
                                @elseif($order->order_status == 'confirmed') bg-blue-100 text-blue-800
                                @elseif($order->order_status == 'processing') bg-yellow-100 text-yellow-800
                                @elseif($order->order_status == 'shipped') bg-purple-100 text-purple-800
                                @elseif($order->order_status == 'delivered') bg-green-100 text-green-800
                                @elseif($order->order_status == 'cancelled') bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst($order->order_status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $order->items->count() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ${{ number_format($order->total_amount, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium no-print">
                            <a href="{{ route('orders.show', $order) }}" class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                            No orders found matching the criteria.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Mobile Cards -->
        <div class="sm:hidden space-y-4 p-4">
            @forelse($orders as $order)
                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                    <!-- Order Header -->
                    <div class="flex items-start justify-between mb-3">
                        <div class="min-w-0 flex-1">
                            <h4 class="text-sm font-medium text-gray-900 truncate">
                                {{ $order->order_number ?? ('#'.$order->id) }}
                            </h4>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $order->created_at->format('M d, Y') }}
                            </p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-lg font-semibold text-gray-900">
                                ${{ number_format($order->total_amount, 2) }}
                            </span>
                            <a href="{{ route('orders.show', $order) }}" class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Order Details -->
                    <div class="space-y-2">
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-user w-4 h-4 mr-2 text-gray-400"></i>
                            <span class="truncate">{{ $order->customer->display_name ?? $order->customer->company_name ?? $order->customer->full_name ?? '—' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-boxes w-4 h-4 mr-2 text-gray-400"></i>
                                <span>{{ $order->items->count() }} items</span>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($order->order_status == 'draft') bg-gray-100 text-gray-800
                                @elseif($order->order_status == 'confirmed') bg-blue-100 text-blue-800
                                @elseif($order->order_status == 'processing') bg-yellow-100 text-yellow-800
                                @elseif($order->order_status == 'shipped') bg-purple-100 text-purple-800
                                @elseif($order->order_status == 'delivered') bg-green-100 text-green-800
                                @elseif($order->order_status == 'cancelled') bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst($order->order_status) }}
                            </span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-8">
                    <i class="fas fa-shopping-cart text-gray-400 text-4xl mb-2"></i>
                    <p class="text-sm text-gray-500">No orders found matching the criteria.</p>
                </div>
            @endforelse
        </div>
        
        @if($orders->hasPages())
        <div class="px-4 sm:px-6 py-4 border-t border-gray-200">
            {{ $orders->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

    <!-- State Distribution Chart -->
    <div class="bg-white shadow rounded-lg print:hidden no-print">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Order State Distribution</h3>
        </div>
        <div class="px-4 sm:px-6 py-4">
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                @php
                    $stateCounts = $orders->groupBy('order_status')->map->count();
                @endphp
                @foreach($states as $state)
                <div class="text-center p-3 bg-gray-50 rounded-lg">
                    <div class="text-xl sm:text-2xl font-bold text-gray-900">{{ $stateCounts->get($state, 0) }}</div>
                    <div class="text-xs sm:text-sm text-gray-500">{{ ucfirst($state) }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
function exportToCSV() {
    // Get current URL with filters
    const url = new URL(window.location.href);
    url.searchParams.set('export', 'csv');
    
    // Create a temporary link to download
    const link = document.createElement('a');
    link.href = url.toString();
    link.download = 'orders-report.csv';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>
@endsection
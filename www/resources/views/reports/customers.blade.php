@extends('layouts.app')

@section('title', 'Customer Analysis Report')

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
                    <h2 class="text-xl font-semibold text-gray-900">Customer Analysis Report</h2>
                    <p class="mt-1 text-sm text-gray-600">Analyze customer performance and identify top customers.</p>
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
                <form method="GET" action="{{ route('reports.customers') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
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
                    <div class="flex items-end space-x-3">
                        <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                            <i class="fas fa-search mr-2"></i>
                            Apply Filters
                        </button>
                        <a href="{{ route('reports.customers') }}" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fas fa-times mr-2"></i>
                            Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 report-cards">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-users text-blue-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Customers</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $customers->total() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-shopping-cart text-green-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Orders</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $customers->sum('orders_count') }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-chart-line text-purple-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Sales</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $customers->sum('sales_count') }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-calculator text-orange-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Avg Orders/Customer</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $customers->count() > 0 ? number_format($customers->sum('orders_count') / $customers->count(), 1) : '0' }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Customers -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Top Customers by Revenue</h3>
            <p class="mt-1 text-sm text-gray-600">Customers ranked by total revenue generated.</p>
        </div>
        
        <!-- Desktop Table -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rank</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Orders</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Revenue</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($topCustomers as $index => $customer)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            <div class="flex items-center">
                                @if($index < 3)
                                    <i class="fas fa-trophy text-yellow-500 mr-2"></i>
                                @endif
                                #{{ $index + 1 }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $customer->company_name ?? $customer->full_name ?? '—' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $customer->email }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ number_format($customer->total_orders) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ${{ number_format($customer->total_revenue, 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                            No customer data available for the selected period.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Mobile Cards -->
        <div class="sm:hidden space-y-4 p-4">
            @forelse($topCustomers as $index => $customer)
                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                    <!-- Customer Header -->
                    <div class="flex items-start justify-between mb-3">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center mb-1">
                                @if($index < 3)
                                    <i class="fas fa-trophy text-yellow-500 mr-2"></i>
                                @endif
                                <span class="text-sm font-medium text-gray-500">#{{ $index + 1 }}</span>
                            </div>
                            <h4 class="text-sm font-medium text-gray-900 truncate">
                                {{ $customer->company_name ?? $customer->full_name ?? '—' }}
                            </h4>
                            <p class="text-xs text-gray-500 mt-1 truncate">
                                {{ $customer->email }}
                            </p>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-semibold text-gray-900">
                                ${{ number_format($customer->total_revenue, 2) }}
                            </div>
                            <div class="text-xs text-gray-500">Total Revenue</div>
                        </div>
                    </div>
                    
                    <!-- Customer Details -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Total Orders:</span>
                            <span class="text-gray-900 font-medium">{{ number_format($customer->total_orders) }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-8">
                    <i class="fas fa-users text-gray-400 text-4xl mb-2"></i>
                    <p class="text-sm text-gray-500">No customer data available for the selected period.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- All Customers -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">All Customers</h3>
        </div>
        
        <!-- Desktop Table -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone 1</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone 2</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orders</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sales</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider print:hidden no-print">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($customers as $customer)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $customer->company_name ?? $customer->full_name ?? '—' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $customer->email }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $customer->phone_number1 ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $customer->phone_number1 ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $customer->orders_count }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $customer->sales_count }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium print:hidden no-print">
                            <a href="{{ route('customers.show', $customer) }}" class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                            No customers found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Mobile Cards -->
        <div class="sm:hidden space-y-4 p-4">
            @forelse($customers as $customer)
                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                    <!-- Customer Header -->
                    <div class="flex items-start justify-between mb-3">
                        <div class="min-w-0 flex-1">
                            <h4 class="text-sm font-medium text-gray-900 truncate">
                                {{ $customer->company_name ?? $customer->full_name ?? '—' }}
                            </h4>
                            <p class="text-xs text-gray-500 mt-1 truncate">
                                {{ $customer->email }}
                            </p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="text-right">
                                <div class="text-sm font-semibold text-gray-900">
                                    {{ $customer->orders_count }} orders
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $customer->sales_count }} sales
                                </div>
                            </div>
                            <a href="{{ route('customers.show', $customer) }}" class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Customer Details -->
                    <div class="space-y-2">
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-phone w-4 h-4 mr-2 text-gray-400"></i>
                            <span class="truncate">{{ $customer->phone_number1 ?? 'N/A' }}</span>
                        </div>
                        @if($customer->phone_number2)
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-phone w-4 h-4 mr-2 text-gray-400"></i>
                            <span class="truncate">{{ $customer->phone_number2 }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-8">
                    <i class="fas fa-users text-gray-400 text-4xl mb-2"></i>
                    <p class="text-sm text-gray-500">No customers found.</p>
                </div>
            @endforelse
        </div>
        
        @if($customers->hasPages())
        <div class="px-4 sm:px-6 py-4 border-t border-gray-200">
            {{ $customers->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

    <!-- Customer Activity Chart -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Customer Activity</h3>
            <p class="mt-1 text-sm text-gray-600">Customer engagement metrics and activity patterns.</p>
        </div>
        <div class="px-4 sm:px-6 py-4">
            <div class="h-48 sm:h-64 flex items-center justify-center bg-gray-50 rounded-lg">
                <div class="text-center">
                    <i class="fas fa-chart-pie text-gray-400 text-3xl sm:text-4xl mb-2"></i>
                    <p class="text-sm sm:text-base text-gray-500">Customer activity chart would be displayed here</p>
                    <p class="text-xs sm:text-sm text-gray-400">Integration with charting library needed</p>
                </div>
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
    link.download = 'customer-analysis-report.csv';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>
@endsection
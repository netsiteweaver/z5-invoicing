@extends('layouts.app')

@section('title', 'Sales Report')

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
                    <h2 class="text-xl font-semibold text-gray-900">Sales Report</h2>
                    <p class="mt-1 text-sm text-gray-600">Analyze sales performance with period filters and best seller analysis.</p>
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
    <div class="bg-white shadow rounded-lg print:hidden no-print">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Filters</h3>
        </div>
        <div class="px-6 py-4">
            <form method="GET" action="{{ route('reports.sales') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label for="period" class="block text-sm font-medium text-gray-700">Period</label>
                    <select name="period" id="period" onchange="toggleCustomDates()" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        @foreach($periods as $key => $label)
                        <option value="{{ $key }}" {{ request('period') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div id="custom-dates" style="display: {{ request('period') == 'custom' ? 'block' : 'none' }};">
                    <label for="date_from" class="block text-sm font-medium text-gray-700">Date From</label>
                    <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
                <div id="custom-dates-to" style="display: {{ request('period') == 'custom' ? 'block' : 'none' }};">
                    <label for="date_to" class="block text-sm font-medium text-gray-700">Date To</label>
                    <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
                <div class="flex items-end space-x-3">
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                        <i class="fas fa-search mr-2"></i>
                        Apply Filters
                    </button>
                    <a href="{{ route('reports.sales') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <i class="fas fa-times mr-2"></i>
                        Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="report-cards">
        <div class="bg-white overflow-hidden shadow rounded-lg report-card">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-chart-line text-blue-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Sales</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $sales->total() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white overflow-hidden shadow rounded-lg report-card">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-dollar-sign text-green-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Revenue</dt>
                            <dd class="text-lg font-medium text-gray-900">${{ number_format($sales->sum('total_amount'), 2) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white overflow-hidden shadow rounded-lg report-card">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-shopping-bag text-purple-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Items Sold</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $sales->sum(function($sale) { return $sale->saleItems->sum('quantity'); }) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white overflow-hidden shadow rounded-lg report-card">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-calculator text-orange-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Average Sale</dt>
                            <dd class="text-lg font-medium text-gray-900">${{ $sales->count() > 0 ? number_format($sales->sum('total_amount') / $sales->count(), 2) : '0.00' }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Table -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Sales</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sale #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider print:hidden no-print">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($sales as $sale)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $sale->sale_number ?? ('#'.$sale->id) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $sale->customer->display_name ?? $sale->customer->company_name ?? $sale->customer->full_name ?? '—' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $sale->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $sale->saleItems->count() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ${{ number_format($sale->total_amount, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium print:hidden no-print">
                            <a href="{{ route('sales.show', $sale) }}" class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                            No sales found matching the criteria.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($sales->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $sales->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

    <!-- Best Sellers -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Best Sellers</h3>
            <p class="mt-1 text-sm text-gray-600">Top performing products by quantity sold and revenue.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rank</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity Sold</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Revenue</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($bestSellers as $index => $product)
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
                            {{ $product->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $product->sku }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ number_format($product->total_quantity) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ${{ number_format($product->total_revenue, 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                            No sales data available for the selected period.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Sales by Brand and Category -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Sales by Brand -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Sales by Brand</h3>
                <p class="mt-1 text-sm text-gray-600">Revenue breakdown by product brand.</p>
            </div>
            <div class="px-6 py-4">
                @if($salesByBrand->count() > 0)
                    <div class="h-80">
                        <canvas id="salesByBrandChart"></canvas>
                    </div>
                @else
                    <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
                        <div class="text-center">
                            <i class="fas fa-tags text-gray-400 text-4xl mb-2"></i>
                            <p class="text-gray-500">No brand sales data available</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sales by Category -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Sales by Category</h3>
                <p class="mt-1 text-sm text-gray-600">Revenue breakdown by product category.</p>
            </div>
            <div class="px-6 py-4">
                @if($salesByCategory->count() > 0)
                    <div class="h-80">
                        <canvas id="salesByCategoryChart"></canvas>
                    </div>
                @else
                    <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
                        <div class="text-center">
                            <i class="fas fa-list text-gray-400 text-4xl mb-2"></i>
                            <p class="text-gray-500">No category sales data available</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Sales Trend Chart -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Sales Trend</h3>
            <p class="mt-1 text-sm text-gray-600">Daily sales performance for the selected period.</p>
        </div>
        <div class="px-6 py-4">
            @if($salesTrend->count() > 0)
                <div class="h-80">
                    <canvas id="salesTrendChart"></canvas>
                </div>
            @else
                <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
                    <div class="text-center">
                        <i class="fas fa-chart-area text-gray-400 text-4xl mb-2"></i>
                        <p class="text-gray-500">No sales found for the selected period</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function toggleCustomDates() {
    const period = document.getElementById('period').value;
    const customDates = document.getElementById('custom-dates');
    const customDatesTo = document.getElementById('custom-dates-to');
    
    if (period === 'custom') {
        customDates.style.display = 'block';
        customDatesTo.style.display = 'block';
    } else {
        customDates.style.display = 'none';
        customDatesTo.style.display = 'none';
    }
}

function exportToCSV() {
    // Get current URL with filters
    const url = new URL(window.location.href);
    url.searchParams.set('export', 'csv');
    
    // Create a temporary link to download
    const link = document.createElement('a');
    link.href = url.toString();
    link.download = 'sales-report.csv';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

document.addEventListener('DOMContentLoaded', function() {
    // Color palette for charts
    const colors = [
        '#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', 
        '#06B6D4', '#84CC16', '#F97316', '#EC4899', '#6B7280',
        '#F43F5E', '#8B5A2B', '#059669', '#DC2626', '#7C3AED'
    ];

    // Sales by Brand Chart
    @if($salesByBrand->count() > 0)
    const salesByBrandData = @json($salesByBrand->map(function($brand) {
        return [
            'name' => $brand->brand_name,
            'quantity' => $brand->total_quantity,
            'revenue' => $brand->total_revenue
        ];
    }));

    const brandCtx = document.getElementById('salesByBrandChart').getContext('2d');
    new Chart(brandCtx, {
        type: 'doughnut',
        data: {
            labels: salesByBrandData.map(item => item.name),
            datasets: [{
                data: salesByBrandData.map(item => item.revenue),
                backgroundColor: colors.slice(0, salesByBrandData.length),
                borderColor: '#ffffff',
                borderWidth: 2,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${label}: $${value.toLocaleString()} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
    @endif

    // Sales by Category Chart
    @if($salesByCategory->count() > 0)
    const salesByCategoryData = @json($salesByCategory->map(function($category) {
        return [
            'name' => $category->category_name,
            'quantity' => $category->total_quantity,
            'revenue' => $category->total_revenue
        ];
    }));

    const categoryCtx = document.getElementById('salesByCategoryChart').getContext('2d');
    new Chart(categoryCtx, {
        type: 'doughnut',
        data: {
            labels: salesByCategoryData.map(item => item.name),
            datasets: [{
                data: salesByCategoryData.map(item => item.revenue),
                backgroundColor: colors.slice(0, salesByCategoryData.length),
                borderColor: '#ffffff',
                borderWidth: 2,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${label}: $${value.toLocaleString()} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
    @endif

    // Sales Trend Chart
    @if($salesTrend->count() > 0)
    const salesTrendData = @json($salesTrendFormatted);

    const trendCtx = document.getElementById('salesTrendChart').getContext('2d');
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: salesTrendData.map(item => item.date),
            datasets: [{
                label: 'Sales Amount ($)',
                data: salesTrendData.map(item => item.amount),
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: 'rgb(59, 130, 246)',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 4,
                yAxisID: 'y'
            }, {
                label: 'Number of Sales',
                data: salesTrendData.map(item => item.count),
                borderColor: 'rgb(16, 185, 129)',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                borderWidth: 2,
                fill: false,
                tension: 0.4,
                pointBackgroundColor: 'rgb(16, 185, 129)',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 3,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            scales: {
                x: { grid: { display: false } },
                y: {
                    position: 'left',
                    ticks: { callback: (v) => '$' + Number(v).toLocaleString() }
                },
                y1: {
                    position: 'right',
                    grid: { drawOnChartArea: false },
                }
            },
            plugins: {
                legend: { position: 'top' },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const val = context.parsed.y;
                            if (context.datasetIndex === 0) {
                                return 'Amount: $' + Number(val).toLocaleString();
                            }
                            return 'Count: ' + val;
                        }
                    }
                }
            }
        }
    });
    @endif
});
</script>
@endpush
@endsection
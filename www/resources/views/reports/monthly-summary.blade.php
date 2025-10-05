@extends('layouts.app')

@section('title', 'Monthly Summary Report')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Monthly Summary Report</h2>
                    <p class="mt-1 text-sm text-gray-600">Comprehensive monthly business overview for {{ Carbon\Carbon::parse($month . '-01')->format('F Y') }}</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('reports.index') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Reports
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Month Selector -->
    <div class="bg-white shadow rounded-lg p-6">
        <form method="GET" class="flex items-center space-x-4">
            <label for="month" class="text-sm font-medium text-gray-700">Select Month:</label>
            <input type="month" name="month" id="month" value="{{ $month }}" 
                   class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Update Report
            </button>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-green-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Total Sales</h3>
                    <p class="text-2xl font-bold text-green-600">${{ number_format($totalSales, 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-shopping-cart text-blue-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Total Orders</h3>
                    <p class="text-2xl font-bold text-blue-600">{{ number_format($totalOrders) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-purple-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">New Customers</h3>
                    <p class="text-2xl font-bold text-purple-600">{{ number_format($totalCustomers) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Daily Sales Chart -->
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Daily Sales Trend</h3>
        <div class="h-80">
            <canvas id="dailySalesChart"></canvas>
        </div>
    </div>

    <!-- Top Products -->
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Top Products This Month</h3>
        @if($topProducts->count() > 0)
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Bar Chart -->
                <div class="h-80">
                    <canvas id="topProductsChart"></canvas>
                </div>
                
                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($topProducts as $index => $product)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                    <div class="flex items-center">
                                        <div class="w-3 h-3 rounded-full mr-2" style="background-color: {{ ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#06B6D4', '#84CC16', '#F97316', '#EC4899', '#6B7280'][$index % 10] }}"></div>
                                        <span class="truncate max-w-32" title="{{ $product->name }}">{{ $product->name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ number_format($product->total_quantity) }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500">${{ number_format($product->total_amount, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="text-center py-8">
                <i class="fas fa-chart-bar text-gray-400 text-4xl mb-4"></i>
                <p class="text-gray-500">No sales data available for this month.</p>
            </div>
        @endif
    </div>

    <!-- Daily Breakdown -->
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Daily Breakdown</h3>
        @if($monthlySales->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sales Count</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Sales</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($monthlySales as $day)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ Carbon\Carbon::parse($day->date)->format('M d, Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($day->daily_count) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${{ number_format($day->daily_total, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8">
                <i class="fas fa-calendar text-gray-400 text-4xl mb-4"></i>
                <p class="text-gray-500">No sales data available for this month.</p>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Prepare chart data
    const chartData = @json($monthlySales->map(function($day) {
        return [
            'label' => Carbon\Carbon::parse($day->date)->format('M d'),
            'sales' => $day->daily_total,
            'count' => $day->daily_count
        ];
    }));

    // Create labels and data arrays
    const labels = chartData.map(item => item.label);
    const salesData = chartData.map(item => item.sales);
    const countData = chartData.map(item => item.count);

    // Chart configuration
    const ctx = document.getElementById('dailySalesChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Daily Sales ($)',
                data: salesData,
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: 'rgb(59, 130, 246)',
                pointBorderColor: 'rgb(255, 255, 255)',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7,
                yAxisID: 'y'
            }, {
                label: 'Number of Sales',
                data: countData,
                borderColor: 'rgb(16, 185, 129)',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                borderWidth: 2,
                fill: false,
                tension: 0.4,
                pointBackgroundColor: 'rgb(16, 185, 129)',
                pointBorderColor: 'rgb(255, 255, 255)',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                x: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Date'
                    },
                    grid: {
                        display: false
                    }
                },
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Sales Amount ($)'
                    },
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Number of Sales'
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: 'white',
                    bodyColor: 'white',
                    borderColor: 'rgba(255, 255, 255, 0.1)',
                    borderWidth: 1,
                    callbacks: {
                        label: function(context) {
                            if (context.datasetIndex === 0) {
                                return 'Sales: $' + context.parsed.y.toLocaleString();
                            } else {
                                return 'Count: ' + context.parsed.y + ' sales';
                            }
                        }
                    }
                }
            }
        }
    });

    // Top Products Bar Chart
    const topProductsData = @json($topProducts->map(function($product) {
        return [
            'name' => $product->name,
            'quantity' => $product->total_quantity,
            'amount' => $product->total_amount
        ];
    }));

    const colors = [
        '#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', 
        '#06B6D4', '#84CC16', '#F97316', '#EC4899', '#6B7280'
    ];

    const topProductsCtx = document.getElementById('topProductsChart').getContext('2d');
    const topProductsChart = new Chart(topProductsCtx, {
        type: 'bar',
        data: {
            labels: topProductsData.map(item => item.name.length > 15 ? item.name.substring(0, 15) + '...' : item.name),
            datasets: [{
                label: 'Quantity Sold',
                data: topProductsData.map(item => item.quantity),
                backgroundColor: colors.slice(0, topProductsData.length).map(color => color + '80'),
                borderColor: colors.slice(0, topProductsData.length),
                borderWidth: 2,
                borderRadius: 6,
                borderSkipped: false,
                yAxisID: 'y'
            }, {
                label: 'Total Amount ($)',
                data: topProductsData.map(item => item.amount),
                backgroundColor: colors.slice(0, topProductsData.length).map(color => color + '40'),
                borderColor: colors.slice(0, topProductsData.length),
                borderWidth: 2,
                borderRadius: 6,
                borderSkipped: false,
                yAxisID: 'y1',
                type: 'line',
                fill: false,
                tension: 0.4,
                pointBackgroundColor: colors.slice(0, topProductsData.length),
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                x: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Products'
                    },
                    grid: {
                        display: false
                    },
                    ticks: {
                        maxRotation: 45,
                        minRotation: 0
                    }
                },
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Quantity Sold'
                    },
                    ticks: {
                        beginAtZero: true,
                        precision: 0
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Total Amount ($)'
                    },
                    ticks: {
                        beginAtZero: true,
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: 'white',
                    bodyColor: 'white',
                    borderColor: 'rgba(255, 255, 255, 0.1)',
                    borderWidth: 1,
                    callbacks: {
                        label: function(context) {
                            if (context.datasetIndex === 0) {
                                return 'Quantity: ' + context.parsed.y.toLocaleString();
                            } else {
                                return 'Amount: $' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
@endsection

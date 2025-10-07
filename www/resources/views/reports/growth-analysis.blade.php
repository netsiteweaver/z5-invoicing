@extends('layouts.app')

@section('title', 'Growth Analysis Report')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                <div>
                    <h2 class="text-lg sm:text-xl font-semibold text-gray-900">Growth Analysis Report</h2>
                    <p class="mt-1 text-sm text-gray-600">Year-over-year growth metrics comparing {{ $year }} vs {{ $previousYear }}</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('reports.index') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 min-h-[44px]">
                        <i class="fas fa-arrow-left mr-2"></i>
                        <span class="hidden sm:inline">Back to Reports</span>
                        <span class="sm:hidden">Back</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Year Selector -->
    <div class="bg-white shadow rounded-lg p-4 sm:p-6">
        <form method="GET" class="flex flex-col sm:flex-row sm:items-center space-y-3 sm:space-y-0 sm:space-x-4">
            <label for="year" class="text-sm font-medium text-gray-700">Select Year:</label>
            <select name="year" id="year" class="flex-1 sm:flex-none border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm min-h-[44px]">
                @for($y = now()->year - 5; $y <= now()->year; $y++)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 min-h-[44px]">
                Update Report
            </button>
        </form>
    </div>

    <!-- Growth Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        <div class="bg-white shadow rounded-lg p-4 sm:p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 {{ $salesGrowth >= 0 ? 'bg-green-100' : 'bg-red-100' }} rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-line {{ $salesGrowth >= 0 ? 'text-green-600' : 'text-red-600' }}"></i>
                    </div>
                </div>
                <div class="ml-3 sm:ml-4 min-w-0 flex-1">
                    <h3 class="text-base sm:text-lg font-medium text-gray-900 truncate">Sales Growth</h3>
                    <p class="text-xl sm:text-2xl font-bold {{ $salesGrowth >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $salesGrowth >= 0 ? '+' : '' }}{{ number_format($salesGrowth, 1) }}%
                    </p>
                    <p class="text-xs sm:text-sm text-gray-500">
                        {{ $year }}: ${{ number_format($currentYearTotal, 2) }}<br>
                        {{ $previousYear }}: ${{ number_format($previousYearTotal, 2) }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-4 sm:p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 {{ $ordersGrowth >= 0 ? 'bg-green-100' : 'bg-red-100' }} rounded-lg flex items-center justify-center">
                        <i class="fas fa-shopping-cart {{ $ordersGrowth >= 0 ? 'text-green-600' : 'text-red-600' }}"></i>
                    </div>
                </div>
                <div class="ml-3 sm:ml-4 min-w-0 flex-1">
                    <h3 class="text-base sm:text-lg font-medium text-gray-900 truncate">Orders Growth</h3>
                    <p class="text-xl sm:text-2xl font-bold {{ $ordersGrowth >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $ordersGrowth >= 0 ? '+' : '' }}{{ number_format($ordersGrowth, 1) }}%
                    </p>
                    <p class="text-xs sm:text-sm text-gray-500">
                        {{ $year }}: {{ number_format($currentYearOrders) }}<br>
                        {{ $previousYear }}: {{ number_format($previousYearOrders) }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-4 sm:p-6 sm:col-span-2 lg:col-span-1">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 {{ $customersGrowth >= 0 ? 'bg-green-100' : 'bg-red-100' }} rounded-lg flex items-center justify-center">
                        <i class="fas fa-users {{ $customersGrowth >= 0 ? 'text-green-600' : 'text-red-600' }}"></i>
                    </div>
                </div>
                <div class="ml-3 sm:ml-4 min-w-0 flex-1">
                    <h3 class="text-base sm:text-lg font-medium text-gray-900 truncate">Customer Growth</h3>
                    <p class="text-xl sm:text-2xl font-bold {{ $customersGrowth >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $customersGrowth >= 0 ? '+' : '' }}{{ number_format($customersGrowth, 1) }}%
                    </p>
                    <p class="text-xs sm:text-sm text-gray-500">
                        {{ $year }}: {{ number_format($currentYearCustomers) }}<br>
                        {{ $previousYear }}: {{ number_format($previousYearCustomers) }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Comparison Chart -->
    <div class="bg-white shadow rounded-lg p-4 sm:p-6">
        <h3 class="text-base sm:text-lg font-medium text-gray-900 mb-4">Monthly Sales Comparison</h3>
        <div class="h-48 sm:h-64 overflow-x-auto">
            <div class="h-full flex items-end justify-between space-x-1 min-w-max">
                @php
                    $maxSales = max($currentYearSales->max('total') ?: 1, $previousYearSales->max('total') ?: 1);
                @endphp
                @for($i = 1; $i <= 12; $i++)
                    @php
                        $current = $currentYearSales->get($i);
                        $previous = $previousYearSales->get($i);
                        $currentHeight = $current ? ($current->total / $maxSales) * 100 : 0;
                        $previousHeight = $previous ? ($previous->total / $maxSales) * 100 : 0;
                    @endphp
                    <div class="flex flex-col items-center flex-1 min-w-[60px] sm:min-w-[80px]">
                        <div class="flex items-end space-x-1 w-full justify-center">
                            <div class="bg-blue-500 rounded-t" style="height: {{ $currentHeight }}%; width: 45%"></div>
                            <div class="bg-gray-400 rounded-t" style="height: {{ $previousHeight }}%; width: 45%"></div>
                        </div>
                        <div class="text-xs text-gray-500 mt-2">{{ Carbon\Carbon::create()->month($i)->format('M') }}</div>
                        @if($current)
                            <div class="text-xs text-blue-600 font-medium text-center">${{ number_format($current->total, 0) }}</div>
                        @endif
                        @if($previous)
                            <div class="text-xs text-gray-500 text-center">${{ number_format($previous->total, 0) }}</div>
                        @endif
                    </div>
                @endfor
            </div>
        </div>
        <div class="flex flex-col sm:flex-row justify-center mt-4 space-y-2 sm:space-y-0 sm:space-x-6">
            <div class="flex items-center justify-center">
                <div class="w-3 h-3 bg-blue-500 rounded mr-2"></div>
                <span class="text-sm text-gray-600">{{ $year }}</span>
            </div>
            <div class="flex items-center justify-center">
                <div class="w-3 h-3 bg-gray-400 rounded mr-2"></div>
                <span class="text-sm text-gray-600">{{ $previousYear }}</span>
            </div>
        </div>
    </div>

    <!-- Top Performing Months -->
    <div class="bg-white shadow rounded-lg p-4 sm:p-6">
        <h3 class="text-base sm:text-lg font-medium text-gray-900 mb-4">Top Performing Months (by Growth)</h3>
        @if($topMonths->count() > 0)
            <!-- Mobile Cards (visible on small screens) -->
            <div class="sm:hidden space-y-3">
                @foreach($topMonths->sortByDesc('growth') as $month)
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="text-sm font-medium text-gray-900">{{ $month['month_name'] }}</h4>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $month['growth'] >= 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $month['growth'] >= 0 ? '+' : '' }}{{ number_format($month['growth'], 1) }}%
                        </span>
                    </div>
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <span class="text-gray-500">{{ $year }} Sales:</span>
                            <div class="font-medium text-gray-900">${{ number_format($month['current_total'], 2) }}</div>
                        </div>
                        <div>
                            <span class="text-gray-500">{{ $previousYear }} Sales:</span>
                            <div class="font-medium text-gray-900">${{ number_format($month['previous_total'], 2) }}</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Desktop Table (hidden on small screens) -->
            <div class="hidden sm:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Month</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $year }} Sales</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $previousYear }} Sales</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Growth</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($topMonths->sortByDesc('growth') as $month)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $month['month_name'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${{ number_format($month['current_total'], 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${{ number_format($month['previous_total'], 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $month['growth'] >= 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $month['growth'] >= 0 ? '+' : '' }}{{ number_format($month['growth'], 1) }}%
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8">
                <i class="fas fa-chart-line text-gray-400 text-4xl mb-4"></i>
                <p class="text-gray-500">No comparative data available for the selected years.</p>
            </div>
        @endif
    </div>

    <!-- Monthly Breakdown -->
    <div class="bg-white shadow rounded-lg p-4 sm:p-6">
        <h3 class="text-base sm:text-lg font-medium text-gray-900 mb-4">Complete Monthly Breakdown</h3>
        
        <!-- Mobile Cards (visible on small screens) -->
        <div class="sm:hidden space-y-3">
            @for($i = 1; $i <= 12; $i++)
                @php
                    $current = $currentYearSales->get($i);
                    $previous = $previousYearSales->get($i);
                    $growth = ($current && $previous && $previous->total > 0) ? (($current->total - $previous->total) / $previous->total) * 100 : 0;
                @endphp
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-sm font-medium text-gray-900">{{ Carbon\Carbon::create()->month($i)->format('F') }}</h4>
                        @if($current && $previous)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $growth >= 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $growth >= 0 ? '+' : '' }}{{ number_format($growth, 1) }}%
                            </span>
                        @else
                            <span class="text-gray-400 text-xs">-</span>
                        @endif
                    </div>
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <span class="text-gray-500">{{ $year }} Sales:</span>
                            <div class="font-medium text-gray-900">{{ $current ? '$' . number_format($current->total, 2) : '-' }}</div>
                        </div>
                        <div>
                            <span class="text-gray-500">{{ $year }} Orders:</span>
                            <div class="font-medium text-gray-900">{{ $current ? number_format($current->count) : '-' }}</div>
                        </div>
                        <div>
                            <span class="text-gray-500">{{ $previousYear }} Sales:</span>
                            <div class="font-medium text-gray-900">{{ $previous ? '$' . number_format($previous->total, 2) : '-' }}</div>
                        </div>
                        <div>
                            <span class="text-gray-500">{{ $previousYear }} Orders:</span>
                            <div class="font-medium text-gray-900">{{ $previous ? number_format($previous->count) : '-' }}</div>
                        </div>
                    </div>
                </div>
            @endfor
        </div>

        <!-- Desktop Table (hidden on small screens) -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Month</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $year }} Sales</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $year }} Orders</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $previousYear }} Sales</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $previousYear }} Orders</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Growth</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @for($i = 1; $i <= 12; $i++)
                        @php
                            $current = $currentYearSales->get($i);
                            $previous = $previousYearSales->get($i);
                            $growth = ($current && $previous && $previous->total > 0) ? (($current->total - $previous->total) / $previous->total) * 100 : 0;
                        @endphp
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ Carbon\Carbon::create()->month($i)->format('F') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $current ? '$' . number_format($current->total, 2) : '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $current ? number_format($current->count) : '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $previous ? '$' . number_format($previous->total, 2) : '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $previous ? number_format($previous->count) : '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($current && $previous)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $growth >= 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $growth >= 0 ? '+' : '' }}{{ number_format($growth, 1) }}%
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

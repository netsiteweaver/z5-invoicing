@extends('layouts.app')

@section('title', 'Payment Analysis Report')

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
                    <h2 class="text-xl font-semibold text-gray-900">Payment Analysis Report</h2>
                    <p class="mt-1 text-sm text-gray-600">Track payments and analyze outstanding amounts.</p>
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
                <form method="GET" action="{{ route('reports.payments') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
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
                        <label for="payment_method" class="block text-sm font-medium text-gray-700">Payment Method</label>
                        <select name="payment_method" id="payment_method" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">All Methods</option>
                            @foreach($paymentTypes as $paymentType)
                            <option value="{{ $paymentType->name }}" {{ request('payment_method') == $paymentType->name ? 'selected' : '' }}>
                                {{ $paymentType->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="hide_zero_totals" value="1" {{ request('hide_zero_totals') ? 'checked' : '' }} 
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Hide zero totals</span>
                        </label>
                    </div>
                    <div class="flex items-end space-x-3">
                        <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                            <i class="fas fa-search mr-2"></i>
                            Apply Filters
                        </button>
                        <a href="{{ route('reports.payments') }}" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
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
                        <i class="fas fa-money-bill-wave text-blue-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Payments</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $payments->total() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-dollar-sign text-green-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Amount</dt>
                            <dd class="text-lg font-medium text-gray-900">${{ number_format($payments->sum('amount'), 2) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Outstanding</dt>
                            <dd class="text-lg font-medium text-gray-900">${{ number_format($outstandingPayments->sum('outstanding_amount'), 2) }}</dd>
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
                            <dt class="text-sm font-medium text-gray-500 truncate">Avg Payment</dt>
                            <dd class="text-lg font-medium text-gray-900">${{ $payments->count() > 0 ? number_format($payments->sum('amount') / $payments->count(), 2) : '0.00' }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Outstanding Payments -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Outstanding Payments</h3>
            <p class="mt-1 text-sm text-gray-600">Sales with outstanding payment amounts.</p>
        </div>
        
        <!-- Desktop Table -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sale</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paid Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Outstanding</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider print:hidden no-print">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($outstandingPayments as $outstanding)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $outstanding->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            #{{ $outstanding->sale_id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ${{ number_format($outstanding->total_amount, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">
                            ${{ number_format($outstanding->paid_amount, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 font-medium">
                            ${{ number_format($outstanding->outstanding_amount, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium print:hidden no-print">
                            <a href="{{ route('sales.show', $outstanding->sale_id) }}" class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                            No outstanding payments found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Mobile Cards -->
        <div class="sm:hidden space-y-4 p-4">
            @forelse($outstandingPayments as $outstanding)
                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                    <!-- Outstanding Payment Header -->
                    <div class="flex items-start justify-between mb-3">
                        <div class="min-w-0 flex-1">
                            <h4 class="text-sm font-medium text-gray-900 truncate">
                                {{ $outstanding->name }}
                            </h4>
                            <p class="text-xs text-gray-500 mt-1">
                                Sale #{{ $outstanding->sale_id }}
                            </p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-lg font-semibold text-red-600">
                                ${{ number_format($outstanding->outstanding_amount, 2) }}
                            </span>
                            <a href="{{ route('sales.show', $outstanding->sale_id) }}" class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Payment Details -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Total Amount:</span>
                            <span class="text-gray-900">${{ number_format($outstanding->total_amount, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Paid Amount:</span>
                            <span class="text-green-600">${{ number_format($outstanding->paid_amount, 2) }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-8">
                    <i class="fas fa-check-circle text-gray-400 text-4xl mb-2"></i>
                    <p class="text-sm text-gray-500">No outstanding payments found.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Payments Table -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">All Payments</h3>
        </div>
        
        <!-- Desktop Table -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sale</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider print:hidden no-print">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($payments as $payment)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $payment->payment_number ?? ('#'.$payment->id) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ optional($payment->sale?->customer)->display_name ?? optional($payment->sale?->customer)->company_name ?? optional($payment->sale?->customer)->full_name ?? '—' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            #{{ $payment->sale_id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $payment->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $payment->payment_method }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ${{ number_format($payment->amount, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium print:hidden no-print">
                            <a href="{{ route('payments.show', $payment) }}" class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                            No payments found matching the criteria.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Mobile Cards -->
        <div class="sm:hidden space-y-4 p-4">
            @forelse($payments as $payment)
                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                    <!-- Payment Header -->
                    <div class="flex items-start justify-between mb-3">
                        <div class="min-w-0 flex-1">
                            <h4 class="text-sm font-medium text-gray-900 truncate">
                                {{ $payment->payment_number ?? ('#'.$payment->id) }}
                            </h4>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $payment->created_at->format('M d, Y') }}
                            </p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-lg font-semibold text-gray-900">
                                ${{ number_format($payment->amount, 2) }}
                            </span>
                            <a href="{{ route('payments.show', $payment) }}" class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Payment Details -->
                    <div class="space-y-2">
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-user w-4 h-4 mr-2 text-gray-400"></i>
                            <span class="truncate">{{ optional($payment->sale?->customer)->display_name ?? optional($payment->sale?->customer)->company_name ?? optional($payment->sale?->customer)->full_name ?? '—' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-shopping-cart w-4 h-4 mr-2 text-gray-400"></i>
                                <span>Sale #{{ $payment->sale_id }}</span>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $payment->payment_method }}
                            </span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-8">
                    <i class="fas fa-money-bill-wave text-gray-400 text-4xl mb-2"></i>
                    <p class="text-sm text-gray-500">No payments found matching the criteria.</p>
                </div>
            @endforelse
        </div>
        
        @if($payments->hasPages())
        <div class="px-4 sm:px-6 py-4 border-t border-gray-200">
            {{ $payments->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

    <!-- Payment Type Summary -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Payment Type Summary</h3>
            <p class="mt-1 text-sm text-gray-600">Total amounts and counts by payment type.</p>
        </div>
        <div class="px-6 py-4">
            @php
                $methodCounts = $payments->groupBy('payment_method')->map->count();
                $methodTotals = $payments->groupBy('payment_method')->map->sum('amount');
            @endphp
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Count</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Average Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Percentage</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php
                            // Calculate totals based on visible payment types
                            $visibleTotals = [];
                            $visibleCounts = [];
                            
                            foreach($paymentTypes as $paymentType) {
                                $count = $methodCounts->get($paymentType->name, 0);
                                $total = $methodTotals->get($paymentType->name, 0);
                                
                                // Include in calculations if not hiding zeros or if has non-zero total
                                if (!request('hide_zero_totals') || $total > 0) {
                                    $visibleTotals[] = $total;
                                    $visibleCounts[] = $count;
                                }
                            }
                            
                            $grandTotal = array_sum($visibleTotals);
                            $grandCount = array_sum($visibleCounts);
                        @endphp
                        @foreach($paymentTypes as $paymentType)
                        @php
                            $count = $methodCounts->get($paymentType->name, 0);
                            $total = $methodTotals->get($paymentType->name, 0);
                            $average = $count > 0 ? $total / $count : 0;
                            $percentage = $grandTotal > 0 ? ($total / $grandTotal) * 100 : 0;
                            
                            // Skip if hide_zero_totals is checked and this payment type has zero total
                            if (request('hide_zero_totals') && $total == 0) {
                                continue;
                            }
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-3">
                                        {{ $paymentType->name }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $count }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                ${{ number_format($total, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ${{ number_format($average, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-full bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                    </div>
                                    <span class="text-sm text-gray-600">{{ number_format($percentage, 1) }}%</span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        
                        <!-- Grand Total Row -->
                        <tr class="bg-gray-50 font-medium">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <strong>Total</strong>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <strong>{{ $grandCount }}</strong>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <strong>${{ number_format($grandTotal, 2) }}</strong>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <strong>${{ $grandCount > 0 ? number_format($grandTotal / $grandCount, 2) : '0.00' }}</strong>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <strong>100.0%</strong>
                            </td>
                        </tr>
                    </tbody>
                </table>
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
    link.download = 'payment-analysis-report.csv';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>
@endsection
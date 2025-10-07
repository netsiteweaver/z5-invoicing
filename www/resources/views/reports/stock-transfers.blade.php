@extends('layouts.app')

@section('title', 'Stock Transfers Report')

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
                    <h2 class="text-xl font-semibold text-gray-900">Stock Transfers Report</h2>
                    <p class="mt-1 text-sm text-gray-600">Track internal stock movements between locations.</p>
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
                <form method="GET" action="{{ route('reports.stock-transfers') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
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
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">All Statuses</option>
                            @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end space-x-3">
                        <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                            <i class="fas fa-search mr-2"></i>
                            Apply Filters
                        </button>
                        <a href="{{ route('reports.stock-transfers') }}" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
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
                        <i class="fas fa-exchange-alt text-blue-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Transfers</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stockTransfers->total() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-boxes text-green-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Items</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stockTransfers->sum(function($transfer) { return $transfer->items->sum('quantity'); }) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-clock text-yellow-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">In Transit</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stockTransfers->where('status', 'in_transit')->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Received</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stockTransfers->where('status', 'received')->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Transfers Table -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Stock Transfers</h3>
        </div>
        
        <!-- Desktop Table -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transfer #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">From</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">To</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider print:hidden no-print">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($stockTransfers as $transfer)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $transfer->transfer_number ?? ('#'.$transfer->id) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $transfer->fromDepartment->name ?? 'Department ' . $transfer->from_department_id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $transfer->toDepartment->name ?? 'Department ' . $transfer->to_department_id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $transfer->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($transfer->status == 'pending') bg-yellow-100 text-yellow-800
                                @elseif($transfer->status == 'in_transit') bg-blue-100 text-blue-800
                                @elseif($transfer->status == 'received') bg-green-100 text-green-800
                                @elseif($transfer->status == 'cancelled') bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $transfer->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $transfer->items->count() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium print:hidden no-print">
                            <a href="{{ route('stock-transfers.show', $transfer) }}" class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                            No stock transfers found matching the criteria.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Mobile Cards -->
        <div class="sm:hidden space-y-4 p-4">
            @forelse($stockTransfers as $transfer)
                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                    <!-- Transfer Header -->
                    <div class="flex items-start justify-between mb-3">
                        <div class="min-w-0 flex-1">
                            <h4 class="text-sm font-medium text-gray-900 truncate">
                                {{ $transfer->transfer_number ?? ('#'.$transfer->id) }}
                            </h4>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $transfer->created_at->format('M d, Y') }}
                            </p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-medium text-gray-900">
                                {{ $transfer->items->count() }} items
                            </span>
                            <a href="{{ route('stock-transfers.show', $transfer) }}" class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Transfer Details -->
                    <div class="space-y-2">
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-arrow-right w-4 h-4 mr-2 text-gray-400"></i>
                            <span class="truncate">{{ $transfer->fromDepartment->name ?? 'Department ' . $transfer->from_department_id }}</span>
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-arrow-left w-4 h-4 mr-2 text-gray-400"></i>
                            <span class="truncate">{{ $transfer->toDepartment->name ?? 'Department ' . $transfer->to_department_id }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Status:</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($transfer->status == 'pending') bg-yellow-100 text-yellow-800
                                @elseif($transfer->status == 'in_transit') bg-blue-100 text-blue-800
                                @elseif($transfer->status == 'received') bg-green-100 text-green-800
                                @elseif($transfer->status == 'cancelled') bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $transfer->status)) }}
                            </span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-8">
                    <i class="fas fa-exchange-alt text-gray-400 text-4xl mb-2"></i>
                    <p class="text-sm text-gray-500">No stock transfers found matching the criteria.</p>
                </div>
            @endforelse
        </div>
        
        @if($stockTransfers->hasPages())
        <div class="px-4 sm:px-6 py-4 border-t border-gray-200">
            {{ $stockTransfers->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

    <!-- Status Distribution -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Status Distribution</h3>
        </div>
        <div class="px-4 sm:px-6 py-4">
            @php
                $statusCounts = $stockTransfers->groupBy('status')->map->count();
            @endphp
            <!-- Desktop Grid -->
            <div class="hidden sm:grid grid-cols-4 gap-4">
                @foreach($statuses as $status)
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ $statusCounts->get($status, 0) }}</div>
                    <div class="text-sm text-gray-500">{{ ucfirst(str_replace('_', ' ', $status)) }}</div>
                </div>
                @endforeach
            </div>
            
            <!-- Mobile Cards -->
            <div class="sm:hidden space-y-3">
                @foreach($statuses as $status)
                @php
                    $count = $statusCounts->get($status, 0);
                    $statusColor = match($status) {
                        'pending' => 'bg-yellow-50 border-yellow-200 text-yellow-800',
                        'in_transit' => 'bg-blue-50 border-blue-200 text-blue-800',
                        'received' => 'bg-green-50 border-green-200 text-green-800',
                        'cancelled' => 'bg-red-50 border-red-200 text-red-800',
                        default => 'bg-gray-50 border-gray-200 text-gray-800'
                    };
                @endphp
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-3 h-3 rounded-full mr-3
                            @if($status == 'pending') bg-yellow-400
                            @elseif($status == 'in_transit') bg-blue-400
                            @elseif($status == 'received') bg-green-400
                            @elseif($status == 'cancelled') bg-red-400
                            @else bg-gray-400
                            @endif">
                        </div>
                        <span class="text-sm font-medium text-gray-900">
                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                        </span>
                    </div>
                    <div class="text-lg font-bold text-gray-900">
                        {{ $count }}
                    </div>
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
    link.download = 'stock-transfers-report.csv';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>
@endsection
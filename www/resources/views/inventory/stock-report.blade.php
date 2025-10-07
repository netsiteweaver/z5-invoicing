@extends('layouts.app')

@section('title', 'Stock Report')
@section('description', 'Comprehensive inventory stock report')

@section('actions')
<a href="{{ route('inventory.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
    <i class="fas fa-arrow-left mr-2"></i>
    Back to Inventory
</a>
@endsection

@section('content')
<!-- Filters -->
<div class="bg-white shadow rounded-lg mb-6">
    <div class="px-4 py-5 sm:p-6">
        <form method="GET" class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div>
                <label for="department_id" class="block text-sm font-medium text-gray-700">Location</label>
                <select name="department_id" id="department_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="">All Locations</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                            {{ $department->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-search mr-2"></i>
                    Filter Report
                </button>
            </div>
            
            <div class="flex items-end">
                <button type="button" onclick="window.print()" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-print mr-2"></i>
                    Print Report
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Report Summary -->
<div class="grid grid-cols-2 gap-4 sm:gap-5 sm:grid-cols-4 mb-6">
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-4 sm:p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-6 h-6 sm:w-8 sm:h-8 bg-blue-500 rounded-md flex items-center justify-center">
                        <i class="fas fa-box text-white text-xs sm:text-sm"></i>
                    </div>
                </div>
                <div class="ml-3 sm:ml-5 w-0 flex-1 min-w-0">
                    <dl>
                        <dt class="text-xs sm:text-sm font-medium text-gray-500 truncate">Total Products</dt>
                        <dd class="text-lg sm:text-xl font-medium text-gray-900">{{ $inventory->count() }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-4 sm:p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-6 h-6 sm:w-8 sm:h-8 bg-green-500 rounded-md flex items-center justify-center">
                        <i class="fas fa-check-circle text-white text-xs sm:text-sm"></i>
                    </div>
                </div>
                <div class="ml-3 sm:ml-5 w-0 flex-1 min-w-0">
                    <dl>
                        <dt class="text-xs sm:text-sm font-medium text-gray-500 truncate">In Stock</dt>
                        <dd class="text-lg sm:text-xl font-medium text-gray-900">{{ $inventory->where('current_stock', '>', 0)->count() }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-4 sm:p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-6 h-6 sm:w-8 sm:h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-white text-xs sm:text-sm"></i>
                    </div>
                </div>
                <div class="ml-3 sm:ml-5 w-0 flex-1 min-w-0">
                    <dl>
                        <dt class="text-xs sm:text-sm font-medium text-gray-500 truncate">Low Stock</dt>
                        <dd class="text-lg sm:text-xl font-medium text-gray-900">{{ $inventory->where('current_stock', '>', 0)->filter(function($item) { return $item->current_stock <= $item->min_stock_level; })->count() }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-4 sm:p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-6 h-6 sm:w-8 sm:h-8 bg-red-500 rounded-md flex items-center justify-center">
                        <i class="fas fa-times-circle text-white text-xs sm:text-sm"></i>
                    </div>
                </div>
                <div class="ml-3 sm:ml-5 w-0 flex-1 min-w-0">
                    <dl>
                        <dt class="text-xs sm:text-sm font-medium text-gray-500 truncate">Out of Stock</dt>
                        <dd class="text-lg sm:text-xl font-medium text-gray-900">{{ $inventory->where('current_stock', 0)->count() }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stock Report Table -->
<div class="bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                <i class="fas fa-chart-bar mr-2"></i>
                Stock Report
            </h3>
            <div class="text-sm text-gray-500">
                Generated on {{ now()->format('M d, Y H:i') }}
            </div>
        </div>
        
        <!-- Desktop Table -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Min Level</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Max Level</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reorder Point</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cost Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Value</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($inventory as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mr-3">
                                        <span class="text-gray-600 font-medium text-sm">{{ substr($item->product->name, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $item->product->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $item->product->sku }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $item->department->name }}</div>
                                <div class="text-sm text-gray-500">{{ $item->department->location }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $item->current_stock }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $item->min_stock_level }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $item->max_stock_level ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $item->reorder_point ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($item->current_stock == 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Out of Stock
                                    </span>
                                @elseif($item->current_stock <= $item->min_stock_level)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Low Stock
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        In Stock
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($item->cost_price)
                                    <div class="text-sm text-gray-900">Rs {{ number_format($item->cost_price, 2) }}</div>
                                @else
                                    <div class="text-sm text-gray-500">Not set</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($item->cost_price)
                                    <div class="text-sm font-medium text-gray-900">Rs {{ number_format($item->current_stock * $item->cost_price, 2) }}</div>
                                @else
                                    <div class="text-sm text-gray-500">N/A</div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Mobile Cards -->
        <div class="sm:hidden space-y-4">
            @foreach($inventory as $item)
                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                    <!-- Product Header -->
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center min-w-0 flex-1">
                            <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                <span class="text-gray-600 font-medium text-sm">{{ substr($item->product->name, 0, 1) }}</span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h3 class="text-sm font-medium text-gray-900 truncate">{{ $item->product->name }}</h3>
                                <p class="text-xs text-gray-500 truncate">{{ $item->product->sku }}</p>
                            </div>
                        </div>
                        <div class="ml-2 flex-shrink-0">
                            @if($item->current_stock == 0)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Out of Stock
                                </span>
                            @elseif($item->current_stock <= $item->min_stock_level)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Low Stock
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    In Stock
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Stock Information -->
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <p class="text-xs text-gray-500">Current Stock</p>
                            <p class="text-sm font-medium text-gray-900">{{ $item->current_stock }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Min Level</p>
                            <p class="text-sm font-medium text-gray-900">{{ $item->min_stock_level }}</p>
                        </div>
                    </div>
                    
                    <!-- Additional Info -->
                    <div class="grid grid-cols-1 gap-2 mb-3">
                        <div>
                            <p class="text-xs text-gray-500">Location</p>
                            <p class="text-sm text-gray-900">{{ $item->department->name }}</p>
                            <p class="text-xs text-gray-500">{{ $item->department->location }}</p>
                        </div>
                        @if($item->max_stock_level)
                            <div>
                                <p class="text-xs text-gray-500">Max Level</p>
                                <p class="text-sm text-gray-900">{{ $item->max_stock_level }}</p>
                            </div>
                        @endif
                        @if($item->reorder_point)
                            <div>
                                <p class="text-xs text-gray-500">Reorder Point</p>
                                <p class="text-sm text-gray-900">{{ $item->reorder_point }}</p>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Pricing -->
                    @if($item->cost_price)
                        <div class="pt-3 border-t border-gray-100">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <p class="text-xs text-gray-500">Cost Price</p>
                                    <p class="text-sm font-medium text-gray-900">Rs {{ number_format($item->cost_price, 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Total Value</p>
                                    <p class="text-sm font-medium text-gray-900">Rs {{ number_format($item->current_stock * $item->cost_price, 2) }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Print Styles -->
<style>
@media print {
    .no-print {
        display: none !important;
    }
    
    body {
        font-size: 12px;
    }
    
    .shadow {
        box-shadow: none !important;
    }
    
    .bg-gray-50 {
        background-color: #f9fafb !important;
    }
}
</style>
@endsection

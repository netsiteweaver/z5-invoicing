@extends('layouts.app')

@section('title', 'Low Stock Alert')
@section('description', 'Products that are running low on stock')

@section('actions')
<a href="{{ route('inventory.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
    <i class="fas fa-arrow-left mr-2"></i>
    Back to Inventory
</a>
@endsection

@section('content')
<!-- Alert Summary -->
<div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="fas fa-exclamation-triangle text-red-400"></i>
        </div>
        <div class="ml-3">
            <h3 class="text-sm font-medium text-red-800">Low Stock Alert</h3>
            <div class="mt-2 text-sm text-red-700">
                <p>{{ $lowStockItems->count() }} products are running low on stock and need immediate attention.</p>
            </div>
        </div>
    </div>
</div>

@if($lowStockItems->count() > 0)
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                    Low Stock Products
                </h3>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                    {{ $lowStockItems->count() }} Items
                </span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Stock</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Min Level</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deficit</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($lowStockItems as $item)
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
                                    @php
                                        $deficit = $item->min_stock_level - $item->current_stock;
                                    @endphp
                                    <div class="text-sm font-medium {{ $deficit > 0 ? 'text-red-600' : 'text-gray-900' }}">
                                        {{ $deficit > 0 ? '-' . $deficit : '0' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($item->current_stock == 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Critical
                                        </span>
                                    @elseif($item->current_stock <= ($item->min_stock_level * 0.5))
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                            High
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Medium
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('inventory.show', $item) }}" class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('inventory.edit', $item) }}" class="text-yellow-600 hover:text-yellow-900">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button onclick="openStockMovementModal({{ $item->id }}, '{{ $item->product->name }}', {{ $item->current_stock }})" class="text-green-600 hover:text-green-900">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-3">
        <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium">{{ $lowStockItems->where('current_stock', 0)->count() }}</h3>
                    <p class="text-red-100">Out of Stock</p>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium">{{ $lowStockItems->where('current_stock', '>', 0)->count() }}</h3>
                    <p class="text-yellow-100">Low Stock</p>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-chart-line text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium">{{ $lowStockItems->sum('min_stock_level') - $lowStockItems->sum('current_stock') }}</h3>
                    <p class="text-blue-100">Total Deficit</p>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="text-center py-12">
                <i class="fas fa-check-circle text-6xl text-green-400 mb-4"></i>
                <h3 class="mt-2 text-sm font-medium text-gray-900">All Good!</h3>
                <p class="mt-1 text-sm text-gray-500">No products are currently running low on stock.</p>
                <div class="mt-6">
                    <a href="{{ route('inventory.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Inventory
                    </a>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Stock Movement Modal -->
<div id="stockMovementModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" x-data="{ open: false }" x-show="open" @click.away="open = false">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Add Stock</h3>
                <button @click="open = false" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="stockMovementForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Product</label>
                    <div id="productName" class="text-sm text-gray-900"></div>
                    <div id="currentStock" class="text-sm text-gray-500"></div>
                </div>
                
                <div class="mb-4">
                    <label for="movement_type" class="block text-sm font-medium text-gray-700 mb-2">Movement Type</label>
                    <select name="movement_type" id="movement_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                        <option value="in">Stock In</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                    <input type="number" name="quantity" id="quantity" min="1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                </div>
                
                <div class="mb-4">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" id="notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="e.g., Restocked from supplier"></textarea>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" @click="open = false" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                        Add Stock
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openStockMovementModal(inventoryId, productName, currentStock) {
    document.getElementById('stockMovementForm').action = `/inventory/${inventoryId}/stock-movement`;
    document.getElementById('productName').textContent = productName;
    document.getElementById('currentStock').textContent = `Current Stock: ${currentStock}`;
    document.getElementById('stockMovementModal').classList.remove('hidden');
}

function closeStockMovementModal() {
    document.getElementById('stockMovementModal').classList.add('hidden');
}
</script>
@endsection

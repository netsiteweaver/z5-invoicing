@extends('layouts.app')

@section('title', 'Inventory Management')
@section('description', 'Manage stock levels across multiple locations')

@section('actions')
<a href="{{ route('inventory.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
    <i class="fas fa-plus mr-2"></i>
    Add Inventory
</a>
<a href="{{ route('inventory.low-stock') }}" class="inline-flex items-center px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
    <i class="fas fa-exclamation-triangle mr-2"></i>
    Low Stock Alert
</a>
@endsection

@section('content')
@if(session('success'))
<div class="mb-4 rounded-md bg-green-50 p-4">
    <div class="flex">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-7.364 7.364a1 1 0 01-1.414 0L3.293 9.836a1 1 0 111.414-1.414l3.222 3.222 6.657-6.657a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
        </div>
        <div class="ml-3"><p class="text-sm font-medium text-green-800">{{ session('success') }}</p></div>
    </div>
  </div>
@endif
@if(session('error'))
  <div class="mb-4 rounded-md bg-red-50 p-4">
    <div class="flex">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-5a1 1 0 112 0v2a1 1 0 11-2 0v-2zm0-6a1 1 0 012 0v4a1 1 0 11-2 0V7z" clip-rule="evenodd"/></svg>
        </div>
        <div class="ml-3"><p class="text-sm font-medium text-red-800">{{ session('error') }}</p></div>
    </div>
  </div>
@endif
@if($errors->any())
  <div class="mb-4 rounded-md bg-red-50 p-4">
    <p class="text-sm font-medium text-red-800">{{ $errors->first() }}</p>
  </div>
@endif
<!-- Filters -->
<div class="bg-white shadow rounded-lg mb-6">
    <div class="px-4 py-5 sm:p-6">
        <form method="GET" class="grid grid-cols-1 gap-4 sm:grid-cols-5">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" 
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                       placeholder="Product name, SKU...">
            </div>
            
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
            
            <div>
                <label for="stock_level" class="block text-sm font-medium text-gray-700">Stock Level</label>
                <select name="stock_level" id="stock_level" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="">All Levels</option>
                    <option value="low" {{ request('stock_level') === 'low' ? 'selected' : '' }}>Low Stock</option>
                    <option value="out" {{ request('stock_level') === 'out' ? 'selected' : '' }}>Out of Stock</option>
                    <option value="available" {{ request('stock_level') === 'available' ? 'selected' : '' }}>Available</option>
                </select>
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-search mr-2"></i>
                    Filter
                </button>
            </div>
            
            <div class="flex items-end">
                <a href="{{ route('inventory.stock-report') }}" class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-chart-bar mr-2"></i>
                    Stock Report
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Inventory Table -->
@if($inventory->count() > 0)
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Stock</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Min Level</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cost Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
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
                                    @if($item->max_stock_level)
                                        <div class="text-sm text-gray-500">Max: {{ $item->max_stock_level }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $item->min_stock_level }}</div>
                                    @if($item->reorder_point)
                                        <div class="text-sm text-gray-500">Reorder: {{ $item->reorder_point }}</div>
                                    @endif
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
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('inventory.show', $item) }}" class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('inventory.edit', $item) }}" class="text-yellow-600 hover:text-yellow-900">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button onclick="openStockMovementModal({{ $item->id }}, '{{ $item->product->name }}', {{ $item->current_stock }})" class="text-green-600 hover:text-green-900">
                                            <i class="fas fa-exchange-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Pagination -->
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $inventory->links() }}
        </div>
    </div>
@else
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="text-center py-12">
                <i class="fas fa-warehouse text-6xl text-gray-400 mb-4"></i>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No inventory found</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by adding inventory for your products.</p>
                <div class="mt-6">
                    <a href="{{ route('inventory.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-plus mr-2"></i>
                        Add Inventory
                    </a>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Stock Movement Modal -->
<div id="stockMovementModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Stock Movement</h3>
                <button onclick="closeStockMovementModal()" class="text-gray-400 hover:text-gray-600">
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
                        <option value="">Select movement type</option>
                        <option value="in">Stock In</option>
                        <option value="out">Stock Out</option>
                        <option value="transfer">Transfer</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                    <input type="number" name="quantity" id="quantity" min="1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                </div>
                
                <div class="mb-4">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" id="notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" @click="open = false" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                        Record Movement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openStockMovementModal(inventoryId, productName, currentStock) {
    document.getElementById('stockMovementForm').action = `{{ url('inventory') }}/${inventoryId}/stock-movement`;
    document.getElementById('productName').textContent = productName;
    document.getElementById('currentStock').textContent = `Current Stock: ${currentStock}`;
    document.getElementById('stockMovementModal').classList.remove('hidden');
}

function closeStockMovementModal() {
    document.getElementById('stockMovementModal').classList.add('hidden');
}

// Lightweight toast
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `fixed bottom-4 right-4 z-50 px-4 py-3 rounded-md shadow-lg text-white ${type === 'success' ? 'bg-green-600' : 'bg-red-600'}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.classList.add('opacity-0');
        toast.style.transition = 'opacity 300ms';
        setTimeout(() => toast.remove(), 300);
    }, 2500);
}

document.addEventListener('DOMContentLoaded', function () {
    @if(session('success'))
        showToast("{{ session('success') }}", 'success');
    @endif
    @if(session('error'))
        showToast("{{ session('error') }}", 'error');
    @endif
    @if($errors->any())
        showToast("{{ $errors->first() }}", 'error');
    @endif
});
</script>
@endsection

@extends('layouts.app')

@section('title', 'Inventory Details')
@section('description', 'View inventory details and stock movements')

@section('actions')
<a href="{{ route('inventory.edit', $inventory) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
    <i class="fas fa-edit mr-2"></i>
    Edit Inventory
</a>
<a href="{{ route('inventory.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
    <i class="fas fa-arrow-left mr-2"></i>
    Back to Inventory
</a>
@endsection

@section('content')
<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <!-- Inventory Details -->
    <div class="lg:col-span-2">
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    <i class="fas fa-info-circle mr-2"></i>
                    Inventory Details
                </h3>
                
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Product</label>
                        <div class="mt-1 flex items-center">
                            <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mr-3">
                                <span class="text-gray-600 font-medium text-sm">{{ substr($inventory->product->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $inventory->product->name }}</div>
                                <div class="text-sm text-gray-500">{{ $inventory->product->stockref }}</div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Location</label>
                        <div class="mt-1">
                            <div class="text-sm font-medium text-gray-900">{{ $inventory->department->name }}</div>
                            <div class="text-sm text-gray-500">{{ $inventory->department->location }}</div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Current Stock</label>
                        <div class="mt-1">
                            <span class="text-2xl font-bold text-gray-900">{{ $inventory->current_stock }}</span>
                            @if($inventory->max_stock_level)
                                <span class="text-sm text-gray-500">/ {{ $inventory->max_stock_level }}</span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Stock Status</label>
                        <div class="mt-1">
                            @if($inventory->current_stock == 0)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Out of Stock
                                </span>
                            @elseif($inventory->current_stock <= $inventory->min_stock_level)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Low Stock
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    In Stock
                                </span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Minimum Stock Level</label>
                        <div class="mt-1 text-sm text-gray-900">{{ $inventory->min_stock_level }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Reorder Point</label>
                        <div class="mt-1 text-sm text-gray-900">{{ $inventory->reorder_point ?? 'Not set' }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Cost Price</label>
                        <div class="mt-1 text-sm text-gray-900">
                            @if($inventory->cost_price)
                                Rs {{ number_format($inventory->cost_price, 2) }}
                            @else
                                Not set
                            @endif
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Selling Price</label>
                        <div class="mt-1 text-sm text-gray-900">
                            @if($inventory->selling_price)
                                Rs {{ number_format($inventory->selling_price, 2) }}
                            @else
                                Not set
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Stock Movement Button -->
                <!-- <div class="mt-6">
                    <button onclick="openStockMovementModal({{ $inventory->id }}, '{{ $inventory->product->name }}', {{ $inventory->current_stock }})" 
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <i class="fas fa-exchange-alt mr-2"></i>
                        Record Stock Movement
                    </button>
                </div> -->
            </div>
        </div>

        <!-- Recent Stock Movements -->
        <div class="mt-6 bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    <i class="fas fa-history mr-2"></i>
                    Recent Stock Movements
                </h3>
                
                @if($stockMovements->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($stockMovements as $movement)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $movement->created_at->format('M d, Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($movement->movement_type === 'in')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Stock In
                                                </span>
                                            @elseif($movement->movement_type === 'out')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    Stock Out
                                                </span>
                                            @elseif($movement->movement_type === 'transfer')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    Transfer
                                                </span>
                                            @elseif($movement->movement_type === 'initial')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    Initial
                                                </span>
                                            @elseif($movement->movement_type === 'adjustment_in')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                    Adjustment In
                                                </span>
                                            @elseif($movement->movement_type === 'adjustment_out')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                    Adjustment Out
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $movement->quantity }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $movement->reference_type }} #{{ $movement->reference_id }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ $movement->notes }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-history text-4xl text-gray-400 mb-4"></i>
                        <p class="text-sm text-gray-500">No stock movements recorded yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="lg:col-span-1">
        <!-- Stock Level Indicator -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    <i class="fas fa-chart-pie mr-2"></i>
                    Stock Level
                </h3>
                
                @if($inventory->max_stock_level)
                    <div class="mb-4">
                        <div class="flex justify-between text-sm text-gray-600 mb-1">
                            <span>Current Stock</span>
                            <span>{{ $inventory->current_stock }} / {{ $inventory->max_stock_level }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min(100, ($inventory->current_stock / $inventory->max_stock_level) * 100) }}%"></div>
                        </div>
                    </div>
                @endif

                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Min Level:</span>
                        <span class="text-sm font-medium">{{ $inventory->min_stock_level }}</span>
                    </div>
                    @if($inventory->reorder_point)
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Reorder Point:</span>
                            <span class="text-sm font-medium">{{ $inventory->reorder_point }}</span>
                        </div>
                    @endif
                    @if($inventory->max_stock_level)
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Max Level:</span>
                            <span class="text-sm font-medium">{{ $inventory->max_stock_level }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    <i class="fas fa-bolt mr-2"></i>
                    Quick Actions
                </h3>
                
                <div class="space-y-3">
                    <!-- <a href="{{ route('inventory.edit', $inventory) }}" class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Inventory
                    </a> -->
                    
                    <!-- <button onclick="openStockMovementModal({{ $inventory->id }}, '{{ $inventory->product->name }}', {{ $inventory->current_stock }})" 
                            class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                        <i class="fas fa-exchange-alt mr-2"></i>
                        Stock Movement
                    </button> -->
                    
                    <a href="{{ route('products.show', $inventory->product) }}" class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <i class="fas fa-box mr-2"></i>
                        View Product
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stock Movement Modal -->
<div id="stockMovementModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" x-data="{ open: false }" x-show="open" @click.away="open = false">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Stock Movement</h3>
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

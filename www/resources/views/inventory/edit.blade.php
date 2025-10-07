@extends('layouts.app')

@section('title', 'Edit Inventory')
@section('description', 'Update inventory details and stock levels')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form method="POST" action="{{ route('inventory.update', $inventory) }}">
                @csrf
                @method('PUT')
                
                <!-- Basic Information Section -->
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            <i class="fas fa-info-circle mr-2"></i>
                            Basic Information
                        </h3>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <!-- Product Info (Read-only) -->
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Product</label>
                                <div class="mt-1 flex items-center p-3 bg-gray-50 rounded-md">
                                    <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                        <span class="text-gray-600 font-medium text-sm">{{ substr($inventory->product->name, 0, 1) }}</span>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="text-sm font-medium text-gray-900 truncate">{{ $inventory->product->name }}</div>
                                        <div class="text-sm text-gray-500 truncate">{{ $inventory->product->sku }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Location Info (Read-only) -->
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Location</label>
                                <div class="mt-1 p-3 bg-gray-50 rounded-md">
                                    <div class="text-sm font-medium text-gray-900">{{ $inventory->department->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $inventory->department->location }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stock Levels Section -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            <i class="fas fa-boxes mr-2"></i>
                            Stock Levels
                        </h3>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <!-- Current Stock -->
                            <div>
                                <label for="current_stock" class="block text-sm font-medium text-gray-700">Current Stock <span class="text-red-500">*</span></label>
                                <input type="number" name="current_stock" id="current_stock" min="0" value="{{ old('current_stock', $inventory->current_stock) }}" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('current_stock') border-red-300 @enderror" required>
                                @error('current_stock')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Changing this will create a stock adjustment movement</p>
                            </div>

                            <!-- Min Stock Level -->
                            <div>
                                <label for="min_stock_level" class="block text-sm font-medium text-gray-700">Minimum Stock Level <span class="text-red-500">*</span></label>
                                <input type="number" name="min_stock_level" id="min_stock_level" min="0" value="{{ old('min_stock_level', $inventory->min_stock_level) }}" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('min_stock_level') border-red-300 @enderror" required>
                                @error('min_stock_level')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Max Stock Level -->
                            <div>
                                <label for="max_stock_level" class="block text-sm font-medium text-gray-700">Maximum Stock Level</label>
                                <input type="number" name="max_stock_level" id="max_stock_level" min="0" value="{{ old('max_stock_level', $inventory->max_stock_level) }}" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('max_stock_level') border-red-300 @enderror">
                                @error('max_stock_level')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Reorder Point -->
                            <div>
                                <label for="reorder_point" class="block text-sm font-medium text-gray-700">Reorder Point</label>
                                <input type="number" name="reorder_point" id="reorder_point" min="0" value="{{ old('reorder_point', $inventory->reorder_point) }}" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('reorder_point') border-red-300 @enderror">
                                @error('reorder_point')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Pricing Section -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            <i class="fas fa-dollar-sign mr-2"></i>
                            Pricing
                        </h3>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <!-- Cost Price -->
                            <div>
                                <label for="cost_price" class="block text-sm font-medium text-gray-700">Cost Price (Rs)</label>
                                <input type="number" name="cost_price" id="cost_price" min="0" step="0.01" value="{{ old('cost_price', $inventory->cost_price) }}" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('cost_price') border-red-300 @enderror">
                                @error('cost_price')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Selling Price -->
                            <div>
                                <label for="selling_price" class="block text-sm font-medium text-gray-700">Selling Price (Rs)</label>
                                <input type="number" name="selling_price" id="selling_price" min="0" step="0.01" value="{{ old('selling_price', $inventory->selling_price) }}" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('selling_price') border-red-300 @enderror">
                                @error('selling_price')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="mt-6 flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3">
                    <a href="{{ route('inventory.show', $inventory) }}" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </a>
                    <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-save mr-2"></i>
                        Update Inventory
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Current Stock Level Indicator -->
    <div class="mt-6 bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                <i class="fas fa-chart-pie mr-2"></i>
                Current Stock Level
            </h3>
            
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <div class="text-2xl font-bold text-blue-600">{{ $inventory->current_stock }}</div>
                    <div class="text-sm text-gray-500">Current Stock</div>
                </div>
                
                <div class="text-center p-4 bg-yellow-50 rounded-lg">
                    <div class="text-2xl font-bold text-yellow-600">{{ $inventory->min_stock_level }}</div>
                    <div class="text-sm text-gray-500">Min Level</div>
                </div>
                
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <div class="text-2xl font-bold text-green-600">{{ $inventory->max_stock_level ?? 'N/A' }}</div>
                    <div class="text-sm text-gray-500">Max Level</div>
                </div>
            </div>

            @if($inventory->max_stock_level)
                <div class="mt-4">
                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                        <span>Stock Level</span>
                        <span>{{ $inventory->current_stock }} / {{ $inventory->max_stock_level }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min(100, ($inventory->current_stock / $inventory->max_stock_level) * 100) }}%"></div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Warning Information -->
    <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">Important Note</h3>
                <div class="mt-2 text-sm text-yellow-700">
                    <p>Changing the current stock will automatically create a stock adjustment movement record. This helps maintain an accurate audit trail of all inventory changes.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

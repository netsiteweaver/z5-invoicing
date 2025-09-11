@extends('layouts.app')

@section('title', 'Add Inventory')
@section('description', 'Add inventory for a product at a specific location')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form method="POST" action="{{ route('inventory.store') }}">
                @csrf
                
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Product Selection -->
                    <div class="sm:col-span-2">
                        <label for="product_id" class="block text-sm font-medium text-gray-700">Product <span class="text-red-500">*</span></label>
                        <select name="product_id" id="product_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('product_id') border-red-300 @enderror" required>
                            <option value="">Select a product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }} ({{ $product->sku }})
                                </option>
                            @endforeach
                        </select>
                        @error('product_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Department Selection -->
                    <div class="sm:col-span-2">
                        <label for="department_id" class="block text-sm font-medium text-gray-700">Location/Department <span class="text-red-500">*</span></label>
                        <select name="department_id" id="department_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('department_id') border-red-300 @enderror" required>
                            <option value="">Select a location</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }} - {{ $department->location }}
                                </option>
                            @endforeach
                        </select>
                        @error('department_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Current Stock -->
                    <div>
                        <label for="current_stock" class="block text-sm font-medium text-gray-700">Current Stock <span class="text-red-500">*</span></label>
                        <input type="number" name="current_stock" id="current_stock" min="0" value="{{ old('current_stock', 0) }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('current_stock') border-red-300 @enderror" required>
                        @error('current_stock')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Min Stock Level -->
                    <div>
                        <label for="min_stock_level" class="block text-sm font-medium text-gray-700">Minimum Stock Level <span class="text-red-500">*</span></label>
                        <input type="number" name="min_stock_level" id="min_stock_level" min="0" value="{{ old('min_stock_level', 0) }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('min_stock_level') border-red-300 @enderror" required>
                        @error('min_stock_level')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Max Stock Level -->
                    <div>
                        <label for="max_stock_level" class="block text-sm font-medium text-gray-700">Maximum Stock Level</label>
                        <input type="number" name="max_stock_level" id="max_stock_level" min="0" value="{{ old('max_stock_level') }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('max_stock_level') border-red-300 @enderror">
                        @error('max_stock_level')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Reorder Point -->
                    <div>
                        <label for="reorder_point" class="block text-sm font-medium text-gray-700">Reorder Point</label>
                        <input type="number" name="reorder_point" id="reorder_point" min="0" value="{{ old('reorder_point') }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('reorder_point') border-red-300 @enderror">
                        @error('reorder_point')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Cost Price -->
                    <div>
                        <label for="cost_price" class="block text-sm font-medium text-gray-700">Cost Price (Rs)</label>
                        <input type="number" name="cost_price" id="cost_price" min="0" step="0.01" value="{{ old('cost_price') }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('cost_price') border-red-300 @enderror">
                        @error('cost_price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Selling Price -->
                    <div>
                        <label for="selling_price" class="block text-sm font-medium text-gray-700">Selling Price (Rs)</label>
                        <input type="number" name="selling_price" id="selling_price" min="0" step="0.01" value="{{ old('selling_price') }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('selling_price') border-red-300 @enderror">
                        @error('selling_price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="mt-6 flex justify-end space-x-3">
                    <a href="{{ route('inventory.index') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-save mr-2"></i>
                        Create Inventory
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Help Information -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-400"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Inventory Setup Guidelines</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li><strong>Current Stock:</strong> The actual quantity available at this location</li>
                        <li><strong>Minimum Stock Level:</strong> Alert threshold - system will notify when stock falls below this</li>
                        <li><strong>Maximum Stock Level:</strong> Maximum capacity for this product at this location</li>
                        <li><strong>Reorder Point:</strong> When to reorder - typically higher than minimum stock level</li>
                        <li><strong>Cost Price:</strong> Your cost for this product (used for profit calculations)</li>
                        <li><strong>Selling Price:</strong> Price you sell this product for at this location</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

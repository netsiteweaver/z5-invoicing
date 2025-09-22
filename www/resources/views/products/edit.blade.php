@extends('layouts.app')

@section('title', 'Edit Product')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center">
        <a href="{{ route('products.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Product</h1>
            <p class="mt-1 text-sm text-gray-500">Update product information</p>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white shadow rounded-lg">
        <form method="POST" action="{{ route('products.update', $product) }}" class="space-y-6 p-6">
            @csrf
            @method('PUT')
            
            <!-- Basic Information -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Product Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Product Name *</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('name') border-red-300 @enderror" 
                               placeholder="Enter product name">
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Barcode -->
                    <div>
                        <label for="barcode" class="block text-sm font-medium text-gray-700">Barcode</label>
                        <input type="text" name="barcode" id="barcode" value="{{ old('barcode', $product->barcode) }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('barcode') border-red-300 @enderror" 
                               placeholder="Barcode number">
                        @error('barcode')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    

                    <!-- Category -->
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700">Category *</label>
                        <select name="category_id" id="category_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('category_id') border-red-300 @enderror">
                            <option value="">Select category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Brand -->
                    <div>
                        <label for="brand_id" class="block text-sm font-medium text-gray-700">Brand *</label>
                        <select name="brand_id" id="brand_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('brand_id') border-red-300 @enderror">
                            <option value="">Select brand</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('brand_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Default UOM -->
                    <div>
                        <label for="uom_id" class="block text-sm font-medium text-gray-700">Default UOM</label>
                        <select name="uom_id" id="uom_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            @foreach(\App\Models\Uom::orderBy('name')->get() as $u)
                                <option value="{{ $u->id }}" {{ old('uom_id', $product->uom_id) == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->units_per_uom }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Description -->
                    <div class="sm:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" id="description" rows="3" 
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('description') border-red-300 @enderror" 
                                  placeholder="Enter product description">{{ old('description', $product->description) }}</textarea>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Pricing Information -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Pricing Information</h3>
                <div class="mb-4 hidden">
                    <label class="inline-flex items-center text-sm text-gray-700">
                        <input type="hidden" name="prices_inclusive" value="0">
                        <input type="checkbox" name="prices_inclusive" value="1" id="prices_inclusive" class="mr-2 border-gray-300 rounded" checked>
                        Enter prices VAT inclusive
                    </label>
                </div>
                
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                    <!-- Cost Price -->
                    <div>
                        <label for="cost_price" class="block text-sm font-medium text-gray-700">Cost Price (VAT incl.) *</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">Rs</span>
                            </div>
                            <input type="number" name="cost_price" id="cost_price" value="{{ old('cost_price', $product->cost_price) }}" 
                                   step="0.01" min="0"
                                   class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-12 pr-12 sm:text-sm border-gray-300 rounded-md @error('cost_price') border-red-300 @enderror" 
                                   placeholder="0.00">
                        </div>
                        <p id="cost_price_excl_note" class="mt-2 text-xs text-gray-500">Excl. VAT: Rs <span id="cost_price_excl_value">0.00</span></p>
                        @error('cost_price')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Selling Price -->
                    <div>
                        <label for="selling_price" class="block text-sm font-medium text-gray-700">Selling Price (VAT incl.) *</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">Rs</span>
                            </div>
                            <input type="number" name="selling_price" id="selling_price" value="{{ old('selling_price', $product->selling_price) }}" 
                                   step="0.01" min="0"
                                   class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-12 pr-12 sm:text-sm border-gray-300 rounded-md @error('selling_price') border-red-300 @enderror" 
                                   placeholder="0.00">
                        </div>
                        <p id="selling_price_excl_note" class="mt-2 text-xs text-gray-500">Excl. VAT: Rs <span id="selling_price_excl_value">0.00</span></p>
                        @error('selling_price')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- VAT Type -->
                    <div>
                        <label for="tax_type" class="block text-sm font-medium text-gray-700">VAT Type</label>
                        <select name="tax_type" id="tax_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('tax_type') border-red-300 @enderror">
                            <option value="standard" {{ old('tax_type', $product->tax_type) === 'standard' ? 'selected' : '' }}>15%</option>
                            <option value="zero" {{ old('tax_type', $product->tax_type) === 'zero' ? 'selected' : '' }}>0%</option>
                            <option value="exempt" {{ old('tax_type', $product->tax_type) === 'exempt' ? 'selected' : '' }}>EX</option>
                        </select>
                        @error('tax_type')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Minimum Selling Price -->
                    <div>
                        <label for="min_selling_price" class="block text-sm font-medium text-gray-700">Min. Selling Price (VAT incl.)</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">Rs</span>
                            </div>
                            <input type="number" name="min_selling_price" id="min_selling_price" value="{{ old('min_selling_price', $product->min_selling_price) }}" 
                                   step="0.01" min="0"
                                   class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-12 pr-12 sm:text-sm border-gray-300 rounded-md @error('min_selling_price') border-red-300 @enderror" 
                                   placeholder="0.00">
                        </div>
                        <p id="min_selling_price_excl_note" class="mt-2 text-xs text-gray-500">Excl. VAT: Rs <span id="min_selling_price_excl_value">0.00</span></p>
                        @error('min_selling_price')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Information</h3>
                
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    

                    

                    

                    <!-- Notes -->
                    <div class="sm:col-span-2">
                        <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea name="notes" id="notes" rows="3" 
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('notes') border-red-300 @enderror" 
                                  placeholder="Additional notes about the product">{{ old('notes', $product->notes) }}</textarea>
                        @error('notes')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Update Product
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Display VAT-inclusive values in inputs while storing exclusive
    const costPriceInput = document.getElementById('cost_price');
    const sellingPriceInput = document.getElementById('selling_price');
    const minSellingPriceInput = document.getElementById('min_selling_price');
    const pricesInclusiveCheckbox = document.getElementById('prices_inclusive');
    const taxTypeSelect = document.getElementById('tax_type');

    const costExclNote = document.getElementById('cost_price_excl_note');
    const costExclValue = document.getElementById('cost_price_excl_value');
    const sellingExclNote = document.getElementById('selling_price_excl_note');
    const sellingExclValue = document.getElementById('selling_price_excl_value');
    
    function calculateProfitMargin() {
        const vat = getVatRate();
        const costBase = parseFloat(costPriceInput.getAttribute('data-base')) || 0;
        const sellBase = parseFloat(sellingPriceInput.getAttribute('data-base')) || 0;
        const costPrice = costBase;
        const sellingPrice = sellBase;
        
        if (costPrice > 0 && sellingPrice > 0) {
            const profitMargin = ((sellingPrice - costPrice) / costPrice) * 100;
            console.log('Profit Margin:', profitMargin.toFixed(1) + '%');
        }
    }
    
    function getVatRate() {
        const taxType = taxTypeSelect.value || 'standard';
        if (taxType === 'standard') return 0.15;
        return 0;
    }

    function updateInclusiveNotes() {
        const vat = getVatRate();
        const baseCost = parseFloat(costPriceInput.getAttribute('data-base')) || parseFloat(costPriceInput.value) || 0;
        const baseSell = parseFloat(sellingPriceInput.getAttribute('data-base')) || parseFloat(sellingPriceInput.value) || 0;

        if (vat > 0) {
            const costIncl = baseCost * (1 + vat);
            const sellIncl = baseSell * (1 + vat);
            // Notes: show exclusive values
            costExclValue.textContent = baseCost.toFixed(2);
            sellingExclValue.textContent = baseSell.toFixed(2);
            costExclNote.classList.remove('hidden');
            sellingExclNote.classList.remove('hidden');
            // Show inputs inclusive
            costPriceInput.value = costIncl.toFixed(2);
            sellingPriceInput.value = sellIncl.toFixed(2);
            if (minSellingPriceInput) {
                const baseMin = parseFloat(minSellingPriceInput.getAttribute('data-base')) || parseFloat(minSellingPriceInput.value) || 0;
                minSellingPriceInput.value = (baseMin * (1 + vat)).toFixed(2);
                const minSellExclValue = document.getElementById('min_selling_price_excl_value');
                if (minSellExclValue) minSellExclValue.textContent = baseMin.toFixed(2);
            }
        } else {
            costExclNote.classList.add('hidden');
            sellingExclNote.classList.add('hidden');
        }
    }

    function handlePriceInput(e) {
        const vat = getVatRate();
        const val = parseFloat(e.target.value) || 0;
        const base = vat > 0 ? val / (1 + vat) : val;
        e.target.setAttribute('data-base', base.toFixed(2));
        calculateProfitMargin();
        // Do not rewrite the field while typing to avoid cursor jumps
        // Only notes are updated here; inputs are recalculated on VAT change/init
        // Update exclusive notes live
        const baseCostNow = parseFloat(costPriceInput.getAttribute('data-base')) || 0;
        const baseSellNow = parseFloat(sellingPriceInput.getAttribute('data-base')) || 0;
        if (costExclValue) costExclValue.textContent = baseCostNow.toFixed(2);
        if (sellingExclValue) sellingExclValue.textContent = baseSellNow.toFixed(2);
        if (minSellingPriceInput) {
            const baseMinNow = parseFloat(minSellingPriceInput.getAttribute('data-base')) || 0;
            const minExclSpan = document.getElementById('min_selling_price_excl_value');
            if (minExclSpan) minExclSpan.textContent = baseMinNow.toFixed(2);
        }
    }

    costPriceInput.addEventListener('input', handlePriceInput);
    sellingPriceInput.addEventListener('input', handlePriceInput);
    if (minSellingPriceInput) {
        minSellingPriceInput.addEventListener('input', handlePriceInput);
    }
    if (pricesInclusiveCheckbox) {
        pricesInclusiveCheckbox.addEventListener('change', updateInclusiveNotes);
    }
    taxTypeSelect.addEventListener('change', updateInclusiveNotes);

    // Initialize base attributes (exclusive as stored)
    costPriceInput.setAttribute('data-base', parseFloat(costPriceInput.value) || 0);
    sellingPriceInput.setAttribute('data-base', parseFloat(sellingPriceInput.value) || 0);
    if (minSellingPriceInput) minSellingPriceInput.setAttribute('data-base', parseFloat(minSellingPriceInput.value) || 0);
    updateInclusiveNotes();
});
</script>
@endsection


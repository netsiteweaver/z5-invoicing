@extends('layouts.app')

@section('title', 'Add Product')

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
            <h1 class="text-2xl font-bold text-gray-900">Add Product</h1>
            <p class="mt-1 text-sm text-gray-500">Create a new product in your catalog</p>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white shadow rounded-lg">
        <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" class="space-y-6 p-6">
            @csrf
            
            <!-- Basic Information -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Product Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Product Name *</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('name') border-red-300 @enderror" 
                               placeholder="Enter product name">
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Barcode -->
                    <div>
                        <label for="barcode" class="block text-sm font-medium text-gray-700">Barcode</label>
                        <div class="mt-1 flex items-center space-x-2">
                            <input type="text" name="barcode" id="barcode" value="{{ old('barcode') }}" 
                                   inputmode="numeric" maxlength="13" autocomplete="off"
                                   class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('barcode') border-red-300 @enderror" 
                                   placeholder="13-digit EAN">
                            <button type="button" id="generate_barcode_btn"
                                    data-country-code="{{ \App\Models\Param::getValue('barcode.country_code', '01') }}"
                                    data-company-code="{{ \App\Models\Param::getValue('barcode.company_code', '001') }}"
                                    class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Generate
                            </button>
                        </div>
                        <p id="generate_hint" class="mt-1 text-xs text-gray-500 hidden">Please select a Brand and a Category before generating a barcode.</p>
                        <p class="mt-1 text-xs text-gray-500">Generates an EAN‑13 barcode only when empty.</p>
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
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Photo -->
                    <div>
                        <label for="photo" class="block text-sm font-medium text-gray-700">Product Image</label>
                        <input type="file" name="photo" id="photo" accept="image/*"
                               class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-md cursor-pointer focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('photo') border-red-300 @enderror">
                        <p class="mt-1 text-xs text-gray-500">JPG, PNG, or WebP up to 2MB.</p>
                        @error('photo')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <div id="photo_preview_wrap" class="mt-2 hidden">
                            <img id="photo_preview" src="#" alt="Preview" class="h-20 w-20 object-cover rounded border">
                        </div>
                    </div>

                    <!-- Brand -->
                    <div>
                        <label for="brand_id" class="block text-sm font-medium text-gray-700">Brand *</label>
                        <select name="brand_id" id="brand_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('brand_id') border-red-300 @enderror">
                            <option value="">Select brand</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ old('brand_id', $defaultBrandId ?? null) == $brand->id ? 'selected' : '' }}>
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
                                <option value="{{ $u->id }}" {{ old('uom_id') == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->units_per_uom }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Description -->
                    <div class="sm:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" id="description" rows="3" 
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('description') border-red-300 @enderror" 
                                  placeholder="Enter product description">{{ old('description') }}</textarea>
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
                            <input type="number" name="cost_price" id="cost_price" value="{{ old('cost_price') }}" 
                                   step="0.01" min="0"
                                   class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-12 pr-12 sm:text-sm border-gray-300 rounded-md @error('cost_price') border-red-300 @enderror" 
                                   placeholder="0.00">
                        </div>
                        <p id="cost_price_excl_note" class="mt-2 text-xs text-gray-500 hidden">Excl. VAT: Rs <span id="cost_price_excl_value">0.00</span></p>
                        @error('cost_price')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Selling Price (VAT exclusive) -->
                    <div>
                        <label for="selling_price" class="block text-sm font-medium text-gray-700">Selling Price (VAT incl.) *</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">Rs</span>
                            </div>
                            <input type="number" name="selling_price" id="selling_price" value="{{ old('selling_price') }}" 
                                   step="0.01" min="0"
                                   class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-12 pr-12 sm:text-sm border-gray-300 rounded-md @error('selling_price') border-red-300 @enderror" 
                                   placeholder="0.00">
                        </div>
                        <p id="selling_price_excl_note" class="mt-2 text-xs text-gray-500 hidden">Excl. VAT: Rs <span id="selling_price_excl_value">0.00</span></p>
                        @error('selling_price')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- VAT Type -->
                    <div>
                        <label for="tax_type" class="block text-sm font-medium text-gray-700">VAT Type</label>
                        <select name="tax_type" id="tax_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('tax_type') border-red-300 @enderror">
                            <option value="standard" {{ old('tax_type', 'standard') === 'standard' ? 'selected' : '' }}>15%</option>
                            <option value="zero" {{ old('tax_type') === 'zero' ? 'selected' : '' }}>0%</option>
                            <option value="exempt" {{ old('tax_type') === 'exempt' ? 'selected' : '' }}>EX</option>
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
                            <input type="number" name="min_selling_price" id="min_selling_price" value="{{ old('min_selling_price') }}" 
                                   step="0.01" min="0"
                                   class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-12 pr-12 sm:text-sm border-gray-300 rounded-md @error('min_selling_price') border-red-300 @enderror" 
                                   placeholder="0.00">
                        </div>
                        <p id="min_selling_price_excl_note" class="mt-2 text-xs text-gray-500 hidden">Excl. VAT: Rs <span id="min_selling_price_excl_value">0.00</span></p>
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
                                  placeholder="Additional notes about the product">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex flex-col sm:flex-row sm:justify-end sm:space-x-3 gap-3 pt-6 border-t border-gray-200">
                <x-action-button type="cancel" :href="route('products.index')" class="w-full sm:w-auto" />
                <x-action-button type="save" class="w-full sm:w-auto">Create Product</x-action-button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Barcode generation (EAN-13) ---
    const barcodeInput = document.getElementById('barcode');
    const genBtn = document.getElementById('generate_barcode_btn');
    const brandSelect = document.getElementById('brand_id');
    const categorySelect = document.getElementById('category_id');
    function enforceBarcodeLength() {
        if (!barcodeInput) return;
        const digits = (barcodeInput.value || '').replace(/\D/g, '').slice(0, 13);
        if (barcodeInput.value !== digits) barcodeInput.value = digits;
    }
    if (barcodeInput) {
        barcodeInput.addEventListener('input', enforceBarcodeLength);
        barcodeInput.addEventListener('blur', enforceBarcodeLength);
        enforceBarcodeLength();
    }
    console.log(barcodeInput, genBtn, brandSelect, categorySelect);
    function padNumber(num, length) {
        const s = String(num ?? '')
            .replace(/\D/g, '')
            .padStart(length, '0');
        return s.slice(-length);
    }
    function ean13CheckDigit(code12) {
        // code12: first 12 digits
        const digits = code12.split('').map(d => parseInt(d, 10));
        const sumOdd = digits.filter((_, i) => i % 2 === 0).reduce((a, b) => a + b, 0);   // positions 1,3,5,7,9,11 (0-indexed even)
        const sumEven = digits.filter((_, i) => i % 2 === 1).reduce((a, b) => a + b, 0);  // positions 2,4,6,8,10,12 (0-indexed odd)
        const total = sumOdd + sumEven * 3;
        const mod = total % 10;
        return (mod === 0 ? 0 : 10 - mod).toString();
    }
    async function fetchSerial(brandId, categoryId) {
        try {
            const url = new URL("{{ route('products.next-serial') }}", window.location.origin);
            url.searchParams.set('brand_id', String(brandId || 0));
            url.searchParams.set('category_id', String(categoryId || 0));
            const resp = await fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (!resp.ok) throw new Error('Failed to fetch serial');
            const data = await resp.json();
            if (!data?.success) throw new Error('Invalid response');
            return String(data.serial || '').replace(/\D/g, '').padStart(3, '0').slice(-3);
        } catch (e) {
            return null;
        }
    }

    async function generatePatternEan13() {
        const countryRaw = genBtn?.getAttribute('data-country-code') || '00';
        const companyRaw = genBtn?.getAttribute('data-company-code') || '000';
        const country = padNumber(countryRaw, 2);     // CC (2)
        const company = padNumber(companyRaw, 3);     // CMP (3)
        let serial = await fetchSerial(parseInt(brandSelect?.value || '0', 10), parseInt(categorySelect?.value || '0', 10));
        if (!serial) {
            serial = padNumber(Math.floor(Math.random()*1000), 3); // fallback
        }
        const brandVal = padNumber((parseInt(brandSelect?.value || '0', 10) % 100), 2);   // BR (2)
        const categoryVal = padNumber((parseInt(categorySelect?.value || '0', 10) % 100), 2); // CT (2)
        const base12 = country + company + brandVal + categoryVal + serial; // total 12
        return base12 + ean13CheckDigit(base12);
    }
    function updateGenerateState() {
        if (!genBtn) return;
        const hasBrand = !!(brandSelect && brandSelect.value);
        const hasCategory = !!(categorySelect && categorySelect.value);
        const hasBoth = hasBrand && hasCategory;
        const hint = document.getElementById('generate_hint');
        if (hint) {
            if (hasBoth) hint.classList.add('hidden'); else hint.classList.remove('hidden');
        }
        // Visual disabled state without blocking click (so we can show message)
        const cls = genBtn.classList;
        if (!hasBoth) {
            cls.add('opacity-50');
            cls.add('cursor-not-allowed');
            genBtn.setAttribute('title', 'Select Brand and Category first');
        } else {
            cls.remove('opacity-50');
            cls.remove('cursor-not-allowed');
            genBtn.removeAttribute('title');
        }
    }

    if (brandSelect) brandSelect.addEventListener('change', updateGenerateState);
    if (categorySelect) categorySelect.addEventListener('change', updateGenerateState);
    updateGenerateState();

    if (genBtn) {
        genBtn.addEventListener('click', async function() {
            if (!barcodeInput) return;
            if (!brandSelect?.value || !categorySelect?.value) {
                const hint = document.getElementById('generate_hint');
                if (hint) hint.classList.remove('hidden');
                (brandSelect?.value ? categorySelect : brandSelect)?.focus();
                return;
            }
            if ((barcodeInput.value || '').trim().length === 0) {
                barcodeInput.value = await generatePatternEan13();
            }
        });
    }

    // Live preview for photo
    const photoInput = document.getElementById('photo');
    const previewImg = document.getElementById('photo_preview');
    const previewWrap = document.getElementById('photo_preview_wrap');
    if (photoInput && previewImg && previewWrap) {
        photoInput.addEventListener('change', function() {
            const file = this.files && this.files[0];
            if (!file) { previewWrap.classList.add('hidden'); return; }
            const reader = new FileReader();
            reader.onload = e => {
                previewImg.src = e.target?.result || '#';
                previewWrap.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        });
    }

    // Maintain VAT-exclusive base values; display inclusive in inputs when requested
    const costPriceInput = document.getElementById('cost_price');
    const sellingPriceInput = document.getElementById('selling_price');
    const minSellingPriceInput = document.getElementById('min_selling_price');
    const pricesInclusiveCheckbox = document.getElementById('prices_inclusive');
    const taxTypeSelect = document.getElementById('tax_type');

    const costExclNote = document.getElementById('cost_price_excl_note');
    const costExclValue = document.getElementById('cost_price_excl_value');
    const sellingExclNote = document.getElementById('selling_price_excl_note');
    const sellingExclValue = document.getElementById('selling_price_excl_value');
    const minSellExclNote = document.getElementById('min_selling_price_excl_note');
    const minSellExclValue = document.getElementById('min_selling_price_excl_value');

    let baseCost = parseFloat(costPriceInput.value) || 0;
    let baseSell = parseFloat(sellingPriceInput.value) || 0;
    let baseMin = minSellingPriceInput ? (parseFloat(minSellingPriceInput.value) || 0) : 0;

    function calculateProfitMargin() {
        const costPrice = baseCost;
        const sellingPrice = baseSell;
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

    function syncDisplayFromBase() {
        const vat = getVatRate();
        const inclusive = pricesInclusiveCheckbox && pricesInclusiveCheckbox.checked && vat > 0;
        if (inclusive) {
            costPriceInput.value = (baseCost * (1 + vat)).toFixed(2);
            sellingPriceInput.value = (baseSell * (1 + vat)).toFixed(2);
            if (minSellingPriceInput) minSellingPriceInput.value = (baseMin * (1 + vat)).toFixed(2);
            // Show exclusive hints
            costExclValue.textContent = baseCost.toFixed(2);
            sellingExclValue.textContent = baseSell.toFixed(2);
            costExclNote.classList.remove('hidden');
            sellingExclNote.classList.remove('hidden');
            if (minSellExclNote && minSellExclValue) {
                minSellExclValue.textContent = baseMin.toFixed(2);
                minSellExclNote.classList.remove('hidden');
            }
        } else {
            costPriceInput.value = baseCost.toFixed(2);
            sellingPriceInput.value = baseSell.toFixed(2);
            if (minSellingPriceInput) minSellingPriceInput.value = baseMin.toFixed(2);
            costExclNote.classList.add('hidden');
            sellingExclNote.classList.add('hidden');
            if (minSellExclNote) minSellExclNote.classList.add('hidden');
        }
        calculateProfitMargin();
    }

    function handleUserInput(e) {
        const vat = getVatRate();
        const inclusive = pricesInclusiveCheckbox && pricesInclusiveCheckbox.checked && vat > 0;
        if (e.target === costPriceInput) {
            const v = parseFloat(costPriceInput.value) || 0;
            baseCost = inclusive ? v / (1 + vat) : v;
        } else if (e.target === sellingPriceInput) {
            const v = parseFloat(sellingPriceInput.value) || 0;
            baseSell = inclusive ? v / (1 + vat) : v;
        } else if (minSellingPriceInput && e.target === minSellingPriceInput) {
            const v = parseFloat(minSellingPriceInput.value) || 0;
            baseMin = inclusive ? v / (1 + vat) : v;
        }
        // Keep hints updated
        costExclValue.textContent = baseCost.toFixed(2);
        sellingExclValue.textContent = baseSell.toFixed(2);
        const minSellExclValue = document.getElementById('min_selling_price_excl_value');
        const minSellExclNote = document.getElementById('min_selling_price_excl_note');
        if (minSellExclValue && minSellExclNote) {
            minSellExclValue.textContent = baseMin.toFixed(2);
        }
    }

    costPriceInput.addEventListener('input', handleUserInput);
    sellingPriceInput.addEventListener('input', handleUserInput);
    if (minSellingPriceInput) minSellingPriceInput.addEventListener('input', handleUserInput);
    if (pricesInclusiveCheckbox) pricesInclusiveCheckbox.addEventListener('change', syncDisplayFromBase);
    taxTypeSelect.addEventListener('change', syncDisplayFromBase);

    // Initialize as inclusive display by default
    syncDisplayFromBase();
});
</script>
@endsection

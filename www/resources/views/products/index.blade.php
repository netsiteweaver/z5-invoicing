@extends('layouts.app')

@section('title', 'Products')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Products</h1>
            <p class="mt-1 text-sm text-gray-500">Manage your product catalog</p>
        </div>
        <x-action-button type="add" href="{{ route('products.create') }}">Add Product</x-action-button>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg" x-data="{ mobileOpen: false }">
        <div class="p-4 sm:p-6">
            <!-- Mobile toggle -->
            <div class="flex items-center justify-between sm:hidden">
                <span class="text-sm font-medium text-gray-700">Filters</span>
                <button type="button" @click="mobileOpen = !mobileOpen" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <span x-show="!mobileOpen"><i class="-ml-1 mr-2 fa-solid fa-sliders"></i>Show</span>
                    <span x-show="mobileOpen"><i class="-ml-1 mr-2 fa-solid fa-xmark"></i>Hide</span>
                </button>
            </div>
            <!-- Form -->
            <div class="mt-4 sm:mt-0" x-cloak x-show="mobileOpen || window.matchMedia('(min-width: 640px)').matches">
                <form method="GET" class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}" 
                               inputmode="search" autofocus
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                               placeholder="Name, stockref, description, or scan barcode (12/13 digits)">
                    </div>
                    
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700">Category</label>
                        <select name="category_id" id="category_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="brand_id" class="block text-sm font-medium text-gray-700">Brand</label>
                        <select name="brand_id" id="brand_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">All Brands</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    
                    <div class="flex items-end space-x-2">
                        <button type="submit" class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="-ml-1 mr-2 fa-solid fa-magnifying-glass"></i>
                            Filter
                        </button>
                        <a href="{{ route('products.index') }}" class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <i class="-ml-1 mr-2 fa-solid fa-rotate-left"></i>
                            Clear
                        </a>
                    </div>
                    <div class="flex items-end">
                        <label class="inline-flex items-center text-sm text-gray-700">
                            <input type="checkbox" id="toggle_barcode" class="mr-2 border-gray-300 rounded" checked>
                            Show barcode
                        </label>
                    </div>
                    <div class="flex items-end">
                        <label class="inline-flex items-center text-sm text-gray-700">
                            <input type="checkbox" id="toggle_image" class="mr-2 border-gray-300 rounded" checked>
                            Show image
                        </label>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Products List -->
    @if($products->count() > 0)
        <!-- Mobile cards -->
        <div class="sm:hidden space-y-4">
            @foreach($products as $product)
                <div class="bg-white shadow rounded-lg p-4">
                    <div class="flex items-start space-x-4">
                        <div class="col-image shrink-0">
                            @if($product->photo)
                                <img src="{{ asset('storage/' . $product->photo) }}" alt="{{ $product->name }}" class="h-16 w-16 rounded object-cover">
                            @else
                                <div class="h-16 w-16 rounded bg-gray-100 flex items-center justify-center text-gray-400">—</div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-base font-semibold text-gray-900 truncate">{{ $product->name }}</div>
                            <div class="mt-1 text-xs text-gray-500 truncate">{{ $product->category->name ?? '-' }} • {{ $product->brand->name ?? '-' }}</div>
                            @php
                                $vatRate = $product->tax_type === 'standard' ? 0.15 : 0;
                                $vatAmount = $product->selling_price * $vatRate;
                                $amountWithVat = $product->selling_price + $vatAmount;
                            @endphp
                            <div class="mt-2 grid grid-cols-2 gap-y-1 text-sm">
                                <div class="text-gray-500">Selling</div>
                                <div class="text-right text-gray-900">Rs {{ number_format($product->selling_price, 2) }}</div>
                                <div class="text-gray-500">Amount w/ VAT</div>
                                <div class="text-right text-gray-900">Rs {{ number_format($amountWithVat, 2) }}</div>
                                <div class="text-gray-500">Stock</div>
                                <div class="text-right text-gray-900">{{ $product->inventory->sum('current_stock') }}</div>
                            </div>
                            <div class="mt-3 col-barcode">
                                @if($product->barcode)
                                    <div class="barcode-holder" data-barcode="{{ $product->barcode }}">
                                        <svg class="barcode-svg"></svg>
                                        <div class="text-[10px] text-gray-400 font-mono mt-1">{{ $product->barcode }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 grid grid-cols-3 gap-2">
                        <a href="{{ route('products.show', $product) }}" class="inline-flex items-center justify-center px-3 py-2 text-sm font-medium rounded-md text-white bg-gray-700 hover:bg-gray-800">
                            <i class="btn-icon fa-regular fa-eye mr-2"></i>View
                        </a>
                        <a href="{{ route('products.edit', $product) }}" class="inline-flex items-center justify-center px-3 py-2 text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            <i class="btn-icon fa-solid fa-pen mr-2"></i>Edit
                        </a>
                        @can('products.delete')
                        <form method="POST" action="{{ route('products.destroy', $product) }}" class="inline"
                              onsubmit="return confirm('Are you sure you want to delete this product?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full inline-flex items-center justify-center px-3 py-2 text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                                <i class="btn-icon fa-solid fa-trash mr-2"></i>Delete
                            </button>
                        </form>
                        @endcan
                    </div>
                </div>
            @endforeach
            <div class="px-4 py-3 border-t border-gray-200">
                {{ $products->links() }}
            </div>
        </div>

        <!-- Desktop/tablet table -->
        <div class="hidden sm:block bg-white shadow rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-max w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="col-barcode px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barcode</th>
                            <th scope="col" class="col-image px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category<br>Brand</th>
                            <!-- <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th> -->
                            <!-- <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">VAT Type</th> -->
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Selling Price</th>
                            <!-- <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">VAT</th> -->
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount w/ VAT</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($products as $product)
                            <tr class="hover:bg-gray-50">
                                <td class="col-barcode px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($product->barcode)
                                        <div class="barcode-holder" data-barcode="{{ $product->barcode }}">
                                            <svg class="barcode-svg"></svg>
                                            <div class="text-xs text-gray-400 font-mono mt-1">{{ $product->barcode }}</div>
                                        </div>
                                    @else
                                        <span class="font-mono">-</span>
                                    @endif
                                </td>
                                <td class="col-image px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($product->photo)
                                    <!-- <img src="{{ Storage::disk('public')->url($product->photo) }}" alt="{{ $product->name }}" class="h-10 w-10 rounded object-cover"> -->
                                    <img src="{{ asset('storage/' . $product->photo) }}" alt="{{ $product->name }}" class="h-10 w-10 rounded object-cover">
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $product->category->name ?? '-' }}<br>{{ $product->brand->name ?? '-' }}</td>
                                <!-- <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"></td> -->
                                <!-- <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @php
                                        $vatLabel = match($product->tax_type) {
                                            'standard' => '15%',
                                            'zero' => '0%',
                                            'exempt' => '0%',
                                            default => ucfirst((string) $product->tax_type),
                                        };
                                    @endphp
                                    {{ $vatLabel }}
                                </td> -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">Rs {{ number_format($product->selling_price, 2) }}</td>
                                @php
                                    $vatRate = $product->tax_type === 'standard' ? 0.15 : 0;
                                    $vatAmount = $product->selling_price * $vatRate;
                                    $amountWithVat = $product->selling_price + $vatAmount;
                                @endphp
                                <!-- <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">Rs {{ number_format($vatAmount, 2) }}</td> -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">Rs {{ number_format($amountWithVat, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">
                                    {{ $product->inventory->sum('current_stock') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        <x-action-button type="view" :href="route('products.show', $product)" />
                                        <x-action-button type="edit" :href="route('products.edit', $product)" />
                                        @can('products.delete')
                                        <x-action-button 
                                            type="delete" 
                                            :form-action="route('products.destroy', $product)"
                                            confirm-message="Are you sure you want to delete this product?"
                                        />
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $products->links() }}
            </div>
        </div>
    @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No products</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by creating a new product.</p>
            <div class="mt-6">
                <x-action-button type="add" href="{{ route('products.create') }}">Add Product</x-action-button>
            </div>
        </div>
    @endif
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggle = document.getElementById('toggle_barcode');
    const toggleImg = document.getElementById('toggle_image');
    const STORAGE_KEYS = { barcode: 'pref_products_show_barcode', image: 'pref_products_show_image' };

    // Initialize from localStorage (defaults: both true)
    const storedBarcode = typeof localStorage !== 'undefined' ? localStorage.getItem(STORAGE_KEYS.barcode) : null;
    const storedImage = typeof localStorage !== 'undefined' ? localStorage.getItem(STORAGE_KEYS.image) : null;
    if (toggle && storedBarcode !== null) toggle.checked = (storedBarcode === '1');
    if (toggleImg && storedImage !== null) toggleImg.checked = (storedImage === '1');
        const update = () => {
            const show = !!(toggle && toggle.checked);
            document.querySelectorAll('.col-barcode').forEach(el => {
                if (show) el.classList.remove('hidden'); else el.classList.add('hidden');
            });
            const showImg = !!(toggleImg && toggleImg.checked);
            document.querySelectorAll('.col-image').forEach(el => {
                if (showImg) el.classList.remove('hidden'); else el.classList.add('hidden');
            });
        };
    if (toggle) {
        toggle.addEventListener('change', function() {
            try { localStorage.setItem(STORAGE_KEYS.barcode, toggle.checked ? '1' : '0'); } catch (e) {}
            update();
        });
    }
    if (toggleImg) {
        toggleImg.addEventListener('change', function() {
            try { localStorage.setItem(STORAGE_KEYS.image, toggleImg.checked ? '1' : '0'); } catch (e) {}
            update();
        });
    }
    update();

    // Render EAN-13 barcodes using minimal JS (no external lib)
    function renderBarcode(svg, code) {
        // Basic validation: EAN-13 digits only, length 12 or 13 (compute checksum if 12)
        let digits = String(code).replace(/\D/g, '');
        if (digits.length === 12) {
            const cd = ean13CheckDigit(digits);
            digits += cd;
        }
        if (digits.length !== 13) return;

        const encodings = {
            L: {
                0: '0001101', 1: '0011001', 2: '0010011', 3: '0111101', 4: '0100011',
                5: '0110001', 6: '0101111', 7: '0111011', 8: '0110111', 9: '0001011'
            },
            G: {
                0: '0100111', 1: '0110011', 2: '0011011', 3: '0100001', 4: '0011101',
                5: '0111001', 6: '0000101', 7: '0010001', 8: '0001001', 9: '0010111'
            },
            R: {
                0: '1110010', 1: '1100110', 2: '1101100', 3: '1000010', 4: '1011100',
                5: '1001110', 6: '1010000', 7: '1000100', 8: '1001000', 9: '1110100'
            }
        };
        const parityMap = {
            0: 'LLLLLL', 1: 'LLGLGG', 2: 'LLGGLG', 3: 'LLGGGL', 4: 'LGLLGG',
            5: 'LGGLLG', 6: 'LGGGLL', 7: 'LGLGLG', 8: 'LGLGGL', 9: 'LGGLGL'
        };

        const first = parseInt(digits[0], 10);
        const left = digits.slice(1, 7).split('').map(d => parseInt(d, 10));
        const right = digits.slice(7).split('').map(d => parseInt(d, 10));
        const parity = parityMap[first];

        let pattern = '101'; // start guard
        for (let i = 0; i < 6; i++) {
            const set = parity[i];
            pattern += encodings[set][left[i]];
        }
        pattern += '01010'; // center guard
        for (let i = 0; i < 6; i++) {
            pattern += encodings.R[right[i]];
        }
        pattern += '101'; // end guard

        // Draw bars
        const barWidth = 1; // px per module
        const barHeight = 28; // px
        svg.setAttribute('width', String(pattern.length * barWidth));
        svg.setAttribute('height', String(barHeight));
        svg.setAttribute('viewBox', `0 0 ${pattern.length * barWidth} ${barHeight}`);
        while (svg.firstChild) svg.removeChild(svg.firstChild);
        let x = 0;
        for (const bit of pattern) {
            if (bit === '1') {
                const rect = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
                rect.setAttribute('x', String(x));
                rect.setAttribute('y', '0');
                rect.setAttribute('width', String(barWidth));
                rect.setAttribute('height', String(barHeight));
                rect.setAttribute('fill', '#111827');
                svg.appendChild(rect);
            }
            x += barWidth;
        }
    }

    function ean13CheckDigit(code12) {
        const digits = code12.split('').map(d => parseInt(d, 10));
        const sumOdd = digits.filter((_, i) => i % 2 === 0).reduce((a, b) => a + b, 0);
        const sumEven = digits.filter((_, i) => i % 2 === 1).reduce((a, b) => a + b, 0);
        const total = sumOdd + sumEven * 3;
        const mod = total % 10;
        return (mod === 0 ? 0 : 10 - mod).toString();
    }

    document.querySelectorAll('.barcode-holder').forEach(holder => {
        const code = holder.getAttribute('data-barcode');
        const svg = holder.querySelector('svg.barcode-svg');
        if (svg && code) renderBarcode(svg, code);
    });
});
</script>
@endsection

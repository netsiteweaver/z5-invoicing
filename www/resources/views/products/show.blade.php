@extends('layouts.app')

@section('title', 'Product Details')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <a href="{{ route('products.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $product->name }}</h1>
                <p class="mt-1 text-sm text-gray-500">{{ $product->stockref }}</p>
            </div>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('products.edit', $product) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Product
            </a>
        </div>
    </div>

    <!-- Product Information -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Main Product Info -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Product Information</h3>
                    
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Product Name</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $product->name }}</dd>
                        </div>
                        
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Category</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $product->category->name ?? 'No category' }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Brand</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $product->brand->name ?? 'No brand' }}</dd>
                        </div>
                        
                        @if($product->sku)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">SKU</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $product->sku }}</dd>
                            </div>
                        @endif
                        
                        @if($product->barcode)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Barcode</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $product->barcode }}</dd>
                            </div>
                        @endif
                        
                        @if($product->weight)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Weight</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $product->weight }} kg</dd>
                            </div>
                        @endif
                        
                        @if($product->dimensions)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Dimensions</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $product->dimensions }}</dd>
                            </div>
                        @endif
                        
                        @if($product->description)
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Description</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $product->description }}</dd>
                            </div>
                        @endif
                        
                        @if($product->notes)
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Notes</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $product->notes }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>
        </div>

        <!-- Pricing & Stock Info -->
        <div class="space-y-6">
            <!-- Pricing Information -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Pricing</h3>
                    
                    <dl class="space-y-4">
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500">Cost Price</dt>
                            <dd class="text-sm text-gray-900">Rs {{ number_format($product->cost_price, 2) }}</dd>
                        </div>
                        
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500">Selling Price</dt>
                            <dd class="text-sm font-medium text-gray-900">Rs {{ number_format($product->selling_price, 2) }}</dd>
                        </div>
                        
                        @if($product->min_selling_price)
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Min. Selling Price</dt>
                                <dd class="text-sm text-gray-900">Rs {{ number_format($product->min_selling_price, 2) }}</dd>
                            </div>
                        @endif
                        
                        @if($product->profit_margin > 0)
                            <div class="flex justify-between border-t pt-4">
                                <dt class="text-sm font-medium text-gray-500">Profit Margin</dt>
                                <dd class="text-sm font-medium text-green-600">{{ number_format($product->profit_margin, 1) }}%</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Stock Information -->
            @if($product->inventory->count() > 0)
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Stock Levels</h3>
                        
                        <div class="space-y-3">
                            @foreach($product->inventory as $inventory)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $inventory->department->name ?? 'Unknown Department' }}</p>
                                        <p class="text-xs text-gray-500">Reorder: {{ $inventory->reorder_point }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-medium {{ $inventory->is_low_stock ? 'text-red-600' : 'text-gray-900' }}">
                                            {{ $inventory->quantity }} units
                                        </p>
                                        @if($inventory->is_low_stock)
                                            <p class="text-xs text-red-500">Low Stock</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-4 pt-4 border-t">
                            <div class="flex justify-between text-sm">
                                <span class="font-medium text-gray-500">Total Stock:</span>
                                <span class="font-medium text-gray-900">{{ $product->total_stock }} units</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="font-medium text-gray-500">Available Stock:</span>
                                <span class="font-medium text-gray-900">{{ $product->available_stock }} units</span>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Stock Levels</h3>
                        <div class="text-center text-gray-500 py-4">
                            <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            <p class="mt-2 text-sm">No inventory records</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

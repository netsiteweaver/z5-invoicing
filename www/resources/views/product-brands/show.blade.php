@extends('layouts.app')

@section('title', 'Brand Details')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <a href="{{ route('product-brands.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $productBrand->name }}</h1>
                <p class="mt-1 text-sm text-gray-500">{{ $productBrand->products->count() }} products</p>
            </div>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('product-brands.edit', $productBrand) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Brand
            </a>
        </div>
    </div>

    <!-- Brand Information -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Main Brand Info -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Brand Information</h3>
                    
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Brand Name</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $productBrand->name }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Sort Order</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $productBrand->sort_order ?? 0 }}</dd>
                        </div>
                        
                        @if($productBrand->website)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Website</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    <a href="{{ $productBrand->website }}" target="_blank" class="text-blue-600 hover:text-blue-900">
                                        {{ $productBrand->website }}
                                    </a>
                                </dd>
                            </div>
                        @endif
                        
                        @if($productBrand->contact_email)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Contact Email</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    <a href="mailto:{{ $productBrand->contact_email }}" class="text-blue-600 hover:text-blue-900">
                                        {{ $productBrand->contact_email }}
                                    </a>
                                </dd>
                            </div>
                        @endif
                        
                        @if($productBrand->contact_phone)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Contact Phone</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    <a href="tel:{{ $productBrand->contact_phone }}" class="text-blue-600 hover:text-blue-900">
                                        {{ $productBrand->contact_phone }}
                                    </a>
                                </dd>
                            </div>
                        @endif
                        
                        @if($productBrand->description)
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Description</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $productBrand->description }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="space-y-6">
            <!-- Statistics -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Statistics</h3>
                    
                    <dl class="space-y-4">
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500">Products</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $productBrand->products->count() }}</dd>
                        </div>
                        
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500">Categories</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $productBrand->products->pluck('category_id')->unique()->count() }}</dd>
                        </div>
                        
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500">Created</dt>
                            <dd class="text-sm text-gray-900">{{ $productBrand->created_at->format('M d, Y') }}</dd>
                        </div>
                        
                        @if($productBrand->updated_at != $productBrand->created_at)
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                                <dd class="text-sm text-gray-900">{{ $productBrand->updated_at->format('M d, Y') }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Contact Actions -->
            @if($productBrand->website || $productBrand->contact_email || $productBrand->contact_phone)
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Contact</h3>
                        
                        <div class="space-y-3">
                            @if($productBrand->website)
                                <a href="{{ $productBrand->website }}" target="_blank" class="flex items-center justify-center w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9" />
                                    </svg>
                                    Visit Website
                                </a>
                            @endif
                            
                            @if($productBrand->contact_email)
                                <a href="mailto:{{ $productBrand->contact_email }}" class="flex items-center justify-center w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    Send Email
                                </a>
                            @endif
                            
                            @if($productBrand->contact_phone)
                                <a href="tel:{{ $productBrand->contact_phone }}" class="flex items-center justify-center w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    Call Phone
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Products by this Brand -->
    @if($productBrand->products->count() > 0)
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Products by {{ $productBrand->name }}</h3>
                
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($productBrand->products->take(6) as $product)
                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">{{ $product->name }}</h4>
                                    <p class="text-xs text-gray-500">{{ $product->stockref }}</p>
                                    <p class="text-xs text-blue-600">{{ $product->category->name ?? 'No category' }}</p>
                                    <p class="text-xs text-green-600">Rs {{ number_format($product->selling_price, 2) }}</p>
                                </div>
                                <a href="{{ route('products.show', $product) }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                    View
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                @if($productBrand->products->count() > 6)
                    <div class="mt-4 text-center">
                        <a href="{{ route('products.index', ['brand_id' => $productBrand->id]) }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                            View all {{ $productBrand->products->count() }} products →
                        </a>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection

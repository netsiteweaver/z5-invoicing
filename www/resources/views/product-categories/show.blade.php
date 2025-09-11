@extends('layouts.app')

@section('title', 'Category Details')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <a href="{{ route('product-categories.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $productCategory->name }}</h1>
                <p class="mt-1 text-sm text-gray-500">
                    @if($productCategory->parent)
                        Subcategory of {{ $productCategory->parent->name }}
                    @else
                        Root Category
                    @endif
                </p>
            </div>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('product-categories.edit', $productCategory) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Category
            </a>
        </div>
    </div>

    <!-- Category Information -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Main Category Info -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Category Information</h3>
                    
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Category Name</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $productCategory->name }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Category Type</dt>
                            <dd class="mt-1">
                                @if($productCategory->parent)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Subcategory
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Root Category
                                    </span>
                                @endif
                            </dd>
                        </div>
                        
                        @if($productCategory->parent)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Parent Category</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $productCategory->parent->name }}</dd>
                            </div>
                        @endif
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Sort Order</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $productCategory->sort_order ?? 0 }}</dd>
                        </div>
                        
                        @if($productCategory->description)
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Description</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $productCategory->description }}</dd>
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
                            <dd class="text-sm font-medium text-gray-900">{{ $productCategory->products->count() }}</dd>
                        </div>
                        
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500">Subcategories</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $productCategory->children->count() }}</dd>
                        </div>
                        
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500">Created</dt>
                            <dd class="text-sm text-gray-900">{{ $productCategory->created_at->format('M d, Y') }}</dd>
                        </div>
                        
                        @if($productCategory->updated_at != $productCategory->created_at)
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                                <dd class="text-sm text-gray-900">{{ $productCategory->updated_at->format('M d, Y') }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Subcategories -->
    @if($productCategory->children->count() > 0)
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Subcategories</h3>
                
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($productCategory->children as $subcategory)
                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">{{ $subcategory->name }}</h4>
                                    <p class="text-xs text-gray-500">{{ $subcategory->products->count() }} products</p>
                                </div>
                                <a href="{{ route('product-categories.show', $subcategory) }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                    View
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Products in this Category -->
    @if($productCategory->products->count() > 0)
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Products in this Category</h3>
                
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($productCategory->products->take(6) as $product)
                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">{{ $product->name }}</h4>
                                    <p class="text-xs text-gray-500">{{ $product->stockref }}</p>
                                    <p class="text-xs text-green-600">Rs {{ number_format($product->selling_price, 2) }}</p>
                                </div>
                                <a href="{{ route('products.show', $product) }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                    View
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                @if($productCategory->products->count() > 6)
                    <div class="mt-4 text-center">
                        <a href="{{ route('products.index', ['category_id' => $productCategory->id]) }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                            View all {{ $productCategory->products->count() }} products →
                        </a>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection

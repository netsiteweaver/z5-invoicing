@extends('layouts.app')

@section('title', 'Sales')
@section('description', 'Manage sales and track revenue')

@section('actions')
<a href="{{ route('sales.create') }}" class="btn btn-create">
    <i class="btn-icon fa-solid fa-plus"></i>
    Create Sale
</a>
@endsection

@section('content')
<!-- Filters -->
<div class="bg-white shadow rounded-lg mb-6" x-data="{ mobileOpen: false }">
    <div class="px-4 py-5 sm:p-6">
        <!-- Mobile toggle -->
        <div class="flex items-center justify-between sm:hidden mb-4">
            <span class="text-sm font-medium text-gray-700">Filters</span>
            <button type="button" @click="mobileOpen = !mobileOpen" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <span x-show="!mobileOpen">Show</span>
                <span x-show="mobileOpen">Hide</span>
                <i class="fas fa-chevron-down ml-2 transform transition-transform" :class="mobileOpen ? 'rotate-180' : ''"></i>
            </button>
        </div>
        
        <!-- Form -->
        <form method="GET" class="grid grid-cols-1 gap-4 sm:grid-cols-6" x-cloak x-show="mobileOpen || window.matchMedia('(min-width: 640px)').matches">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" 
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                       placeholder="Sale #, customer name...">
            </div>
            
            <div>
                <label for="customer_id" class="block text-sm font-medium text-gray-700">Customer</label>
                <select name="customer_id" id="customer_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="">All Customers</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                            {{ $customer->display_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="">All Status</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>Shipped</option>
                    <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            
            <div>
                <label for="payment_status" class="block text-sm font-medium text-gray-700">Payment Status</label>
                <select name="payment_status" id="payment_status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="">All Payment Status</option>
                    <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="partial" {{ request('payment_status') === 'partial' ? 'selected' : '' }}>Partial</option>
                    <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="overdue" {{ request('payment_status') === 'overdue' ? 'selected' : '' }}>Overdue</option>
                </select>
            </div>
            
            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700">From Date</label>
                <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" 
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-search mr-2"></i>
                    Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Sales Table -->
@if($sales->count() > 0)
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            
            <!-- Mobile cards (visible on small screens) -->
            <div class="sm:hidden space-y-4">
                @foreach($sales as $sale)
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mr-3">
                                <span class="text-gray-600 font-medium text-sm">{{ substr($sale->customer->display_name, 0, 1) }}</span>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $sale->sale_number }}</div>
                                <div class="text-xs text-gray-500">{{ $sale->customer->display_name }}</div>
                            </div>
                        </div>
                        <div class="flex flex-col space-y-1">
                            @if($sale->status === 'draft')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{{ ucfirst($sale->status) }}</span>
                            @elseif($sale->status === 'confirmed')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{{ ucfirst($sale->status) }}</span>
                            @elseif($sale->status === 'shipped')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">{{ ucfirst($sale->status) }}</span>
                            @elseif($sale->status === 'delivered')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">{{ ucfirst($sale->status) }}</span>
                            @elseif($sale->status === 'cancelled')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">{{ ucfirst($sale->status) }}</span>
                            @endif
                            @if($sale->payment_status === 'pending')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">{{ ucfirst($sale->payment_status) }}</span>
                            @elseif($sale->payment_status === 'partial')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{{ ucfirst($sale->payment_status) }}</span>
                            @elseif($sale->payment_status === 'paid')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">{{ ucfirst($sale->payment_status) }}</span>
                            @elseif($sale->payment_status === 'overdue')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">{{ ucfirst($sale->payment_status) }}</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <div class="text-xs text-gray-500 mb-1">Date</div>
                            <div class="text-sm text-gray-900">{{ $sale->sale_date ? \Carbon\Carbon::parse($sale->sale_date)->format('M d, Y') : 'N/A' }}</div>
                            @if($sale->due_date)
                                <div class="text-xs text-gray-500">Due: {{ \Carbon\Carbon::parse($sale->due_date)->format('M d, Y') }}</div>
                            @endif
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 mb-1">Total</div>
                            <div class="text-sm font-medium text-gray-900">Rs {{ number_format($sale->total_amount, 2) }}</div>
                            @if($sale->discount_amount > 0)
                                <div class="text-xs text-green-600">-Rs {{ number_format($sale->discount_amount, 2) }}</div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="text-xs text-gray-500 mb-1">Items</div>
                        <div class="text-sm text-gray-900">{{ $sale->items->count() }} items</div>
                    </div>
                    
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('sales.show', $sale) }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 rounded-md transition-colors">
                            <i class="fas fa-eye mr-1"></i> View
                        </a>
                        @if($sale->canBeEdited())
                            <a href="{{ route('sales.edit', $sale) }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-yellow-600 hover:text-yellow-900 bg-yellow-50 hover:bg-yellow-100 rounded-md transition-colors">
                                <i class="fas fa-edit mr-1"></i> Edit
                            </a>
                        @endif
                        @if($sale->payment_status !== 'paid')
                            <a href="{{ route('sales.payments.create', $sale) }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-green-600 hover:text-green-900 bg-green-50 hover:bg-green-100 rounded-md transition-colors">
                                <i class="fas fa-money-bill-wave mr-1"></i> Payment
                            </a>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Desktop table (hidden on small screens) -->
            <div class="hidden sm:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sale #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($sales as $sale)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $sale->sale_number }}</div>
                                    <div class="text-sm text-gray-500">{{ $sale->items->count() }} items</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mr-3">
                                            <span class="text-gray-600 font-medium text-sm">{{ substr($sale->customer->display_name, 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $sale->customer->display_name }}</div>
                                            <div class="text-sm text-gray-500">{{ $sale->customer->customer_type }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $sale->sale_date ? \Carbon\Carbon::parse($sale->sale_date)->format('M d, Y') : 'N/A' }}</div>
                                    @if($sale->due_date)
                                        <div class="text-sm text-gray-500">Due: {{ \Carbon\Carbon::parse($sale->due_date)->format('M d, Y') }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($sale->status === 'draft')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{{ ucfirst($sale->status) }}</span>
                                    @elseif($sale->status === 'confirmed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{{ ucfirst($sale->status) }}</span>
                                    @elseif($sale->status === 'shipped')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">{{ ucfirst($sale->status) }}</span>
                                    @elseif($sale->status === 'delivered')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">{{ ucfirst($sale->status) }}</span>
                                    @elseif($sale->status === 'cancelled')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">{{ ucfirst($sale->status) }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($sale->payment_status === 'pending')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">{{ ucfirst($sale->payment_status) }}</span>
                                    @elseif($sale->payment_status === 'partial')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{{ ucfirst($sale->payment_status) }}</span>
                                    @elseif($sale->payment_status === 'paid')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">{{ ucfirst($sale->payment_status) }}</span>
                                    @elseif($sale->payment_status === 'overdue')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">{{ ucfirst($sale->payment_status) }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">Rs {{ number_format($sale->total_amount, 2) }}</div>
                                    @if($sale->discount_amount > 0)
                                        <div class="text-sm text-green-600">-Rs {{ number_format($sale->discount_amount, 2) }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('sales.show', $sale) }}" class="btn btn-view">
                                            <i class="btn-icon fa-regular fa-eye"></i>
                                            View
                                        </a>
                                        @if($sale->canBeEdited())
                                            <a href="{{ route('sales.edit', $sale) }}" class="btn btn-edit">
                                                <i class="btn-icon fa-solid fa-pen"></i>
                                                Edit
                                            </a>
                                        @endif
                                        @if($sale->payment_status !== 'paid')
                                            <a href="{{ route('sales.payments.create', $sale) }}" class="btn btn-primary" title="Record Payment">
                                                <i class="btn-icon fa-solid fa-money-bill-wave"></i>
                                                Payment
                                            </a>
                                        @endif
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
            {{ $sales->links() }}
        </div>
    </div>
@else
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="text-center py-12">
                <i class="fas fa-chart-line text-6xl text-gray-400 mb-4"></i>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No sales found</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating a new sale or converting an order.</p>
                <div class="mt-6">
                    <a href="{{ route('sales.create') }}" class="btn btn-create">
                        <i class="btn-icon fa-solid fa-plus"></i>
                        Create Sale
                    </a>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection

@extends('layouts.app')

@section('title', 'Orders')
@section('description', 'Manage customer orders and track their status')

@section('actions')
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Orders</h1>
            <p class="mt-1 text-sm text-gray-500">Manage customer orders and track their status</p>
        </div>
        <a href="{{ route('orders.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <i class="-ml-1 mr-2 fa-solid fa-plus"></i>
            Create Order
        </a>
    </div>

    <!-- Filters -->
<div class="bg-white shadow rounded-lg mb-6" x-data="{ mobileOpen: false }">
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
            <form method="GET" class="grid grid-cols-1 gap-4 sm:grid-cols-6">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" 
                           inputmode="search" autofocus
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                           placeholder="Order #, customer name...">
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
                    <label for="status" class="block text-sm font-medium text-gray-700">Order Status</label>
                    <select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">All Status</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>Shipped</option>
                        <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700">From Date</label>
                    <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
                
                <div class="flex items-end space-x-2">
                    <button type="submit" class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="-ml-1 mr-2 fa-solid fa-magnifying-glass"></i>
                        Filter
                    </button>
                    <a href="{{ route('orders.index') }}" class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <i class="-ml-1 mr-2 fa-solid fa-rotate-left"></i>
                        Clear
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Orders List -->
@if($orders->count() > 0)
    <!-- Mobile cards -->
    <div class="sm:hidden space-y-4">
        @foreach($orders as $order)
            <div class="bg-white shadow rounded-lg p-4">
                <div class="flex items-start space-x-4">
                    <div class="shrink-0">
                        <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center">
                            <span class="text-gray-600 font-medium text-lg">{{ substr($order->customer->display_name, 0, 1) }}</span>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-base font-semibold text-gray-900 truncate">{{ $order->order_number }}</div>
                        <div class="mt-1 text-sm text-gray-500 truncate">{{ $order->customer->display_name }} • {{ $order->customer->customer_type }}</div>
                        <div class="mt-2 grid grid-cols-2 gap-y-1 text-sm">
                            <div class="text-gray-500">Date</div>
                            <div class="text-right text-gray-900">{{ $order->order_date ? \Carbon\Carbon::parse($order->order_date)->format('M d, Y') : 'N/A' }}</div>
                            <div class="text-gray-500">Items</div>
                            <div class="text-right text-gray-900">{{ $order->items->count() }} items</div>
                            <div class="text-gray-500">Total</div>
                            <div class="text-right text-gray-900">Rs {{ number_format($order->total_amount, 0) }}</div>
                            @if($order->discount_amount > 0)
                                <div class="text-gray-500">Discount</div>
                                <div class="text-right text-green-600">-Rs {{ number_format($order->discount_amount, 0) }}</div>
                            @endif
                            @if($order->manual_invoice_number)
                                <div class="text-gray-500">Manual #</div>
                                <div class="text-right text-gray-900">{{ $order->manual_invoice_number }}</div>
                            @endif
                        </div>
                        <div class="mt-2">
                            @if($order->order_status === 'draft')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{{ ucfirst($order->order_status) }}</span>
                            @elseif($order->order_status === 'pending')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">{{ ucfirst($order->order_status) }}</span>
                            @elseif($order->order_status === 'confirmed')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{{ ucfirst($order->order_status) }}</span>
                            @elseif($order->order_status === 'processing')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">{{ ucfirst($order->order_status) }}</span>
                            @elseif($order->order_status === 'shipped')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">{{ ucfirst($order->order_status) }}</span>
                            @elseif($order->order_status === 'delivered')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">{{ ucfirst($order->order_status) }}</span>
                            @elseif($order->order_status === 'cancelled')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">{{ ucfirst($order->order_status) }}</span>
                            @endif
                        </div>
                        @if($order->delivery_date)
                            <div class="mt-1 text-xs text-gray-500">Deliver: {{ \Carbon\Carbon::parse($order->delivery_date)->format('M d, Y') }}</div>
                        @endif
                    </div>
                </div>
                <div class="mt-4 grid grid-cols-2 gap-2">
                    <a href="{{ route('orders.show', $order) }}" class="inline-flex items-center justify-center px-3 py-2 text-sm font-medium rounded-md text-white bg-gray-700 hover:bg-gray-800">
                        <i class="btn-icon fa-regular fa-eye mr-2"></i>View
                    </a>
                    @if($order->canBeEdited() && (auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('orders.edit')))
                        <a href="{{ route('orders.edit', $order) }}" class="inline-flex items-center justify-center px-3 py-2 text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            <i class="btn-icon fa-solid fa-pen mr-2"></i>Edit
                        </a>
                    @endif
                    @if($order->canBeEdited() && (auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('orders.delete')))
                        <form method="POST" action="{{ route('orders.destroy', $order) }}" class="inline" onsubmit="return confirm('Delete this order? This is a soft delete and can\'t be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full inline-flex items-center justify-center px-3 py-2 text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                                <i class="btn-icon fa-solid fa-trash mr-2"></i>Delete
                            </button>
                        </form>
                    @endif
                    @if($order->order_status === 'confirmed' && ($order->sales_count ?? 0) === 0 && (auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('orders.convert_to_sale')))
                        <a href="{{ route('orders.convert-to-sale', $order) }}" class="inline-flex items-center justify-center px-3 py-2 text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700" title="Convert to Sale">
                            <i class="btn-icon fa-solid fa-check mr-2"></i>Convert
                        </a>
                    @endif
                </div>
            </div>
        @endforeach
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $orders->links() }}
        </div>
    </div>

    <!-- Desktop/tablet table -->
    <div class="hidden sm:block bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($orders as $order)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
								<div class="text-sm font-medium text-gray-900">{{ $order->order_number }}</div>
								<div class="text-sm text-gray-500">{{ $order->items->count() }} items</div>
								@if($order->manual_invoice_number)
									<div class="text-xs text-gray-500">Manual #: {{ $order->manual_invoice_number }}</div>
								@endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mr-3">
                                            <span class="text-gray-600 font-medium text-sm">{{ substr($order->customer->display_name, 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $order->customer->display_name }}</div>
                                            <div class="text-sm text-gray-500">{{ $order->customer->customer_type }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $order->order_date ? \Carbon\Carbon::parse($order->order_date)->format('M d, Y') : 'N/A' }}</div>
                                    @if($order->delivery_date)
                                        <div class="text-sm text-gray-500">Deliver: {{ \Carbon\Carbon::parse($order->delivery_date)->format('M d, Y') }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($order->order_status === 'draft')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{{ ucfirst($order->order_status) }}</span>
                                    @elseif($order->order_status === 'pending')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">{{ ucfirst($order->order_status) }}</span>
                                    @elseif($order->order_status === 'confirmed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{{ ucfirst($order->order_status) }}</span>
                                    @elseif($order->order_status === 'processing')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">{{ ucfirst($order->order_status) }}</span>
                                    @elseif($order->order_status === 'shipped')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">{{ ucfirst($order->order_status) }}</span>
                                    @elseif($order->order_status === 'delivered')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">{{ ucfirst($order->order_status) }}</span>
                                    @elseif($order->order_status === 'cancelled')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">{{ ucfirst($order->order_status) }}</span>
                                    @endif
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-right text-gray-900">Rs {{ number_format($order->total_amount, 0) }}</div>
                                    @if($order->discount_amount > 0)
                                        <div class="text-sm text-right text-green-600">-Rs {{ number_format($order->discount_amount, 0) }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('orders.show', $order) }}" class="btn btn-view">
                                            <i class="btn-icon fa-regular fa-eye"></i>
                                            View
                                        </a>
                                        @if($order->canBeEdited() && (auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('orders.edit')))
                                            <a href="{{ route('orders.edit', $order) }}" class="btn btn-edit">
                                                <i class="btn-icon fa-solid fa-pen"></i>
                                                Edit
                                            </a>
                                            <form method="POST" action="{{ route('orders.destroy', $order) }}" onsubmit="return confirm('Delete this order? This is a soft delete and can\'t be undone.');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('orders.delete'))
                                                <button type="submit" class="btn btn-delete">
                                                    <i class="btn-icon fa-solid fa-trash"></i>
                                                    Delete
                                                </button>
                                                @endif
                                            </form>
                                        @endif
                                        @if($order->order_status === 'confirmed' && ($order->sales_count ?? 0) === 0 && (auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('orders.convert_to_sale')))
                                            <a href="{{ route('orders.convert-to-sale', $order) }}" class="btn btn-primary" title="Convert to Sale">
                                                <i class="btn-icon fa-solid fa-check"></i>
                                                Convert
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
            {{ $orders->links() }}
        </div>
    </div>
@else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No orders</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by creating a new order.</p>
            <div class="mt-6">
                <a href="{{ route('orders.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Create Order
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
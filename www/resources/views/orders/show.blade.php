@extends('layouts.app')

@section('title', 'Order Details')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-md bg-green-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-7.364 7.364a1 1 0 01-1.414 0L3.293 9.836a1 1 0 111.414-1.414l3.222 3.222 6.657-6.657a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="rounded-md bg-red-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-5a1 1 0 112 0v2a1 1 0 11-2 0v-2zm0-6a1 1 0 012 0v4a1 1 0 11-2 0V7z" clip-rule="evenodd"/></svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Order Details</h1>
            <p class="mt-1 text-sm text-gray-500">Order #{{ $order->order_number }}</p>
        </div>
        <div class="flex space-x-3">
            @if($order->canBeEdited())
                <a href="{{ route('orders.edit', $order) }}" 
                   class="inline-flex items-center h-10 px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Order
                </a>
            @endif
            @if($order->canBeEdited())
                <form method="POST" action="{{ route('orders.destroy', $order) }}" onsubmit="return confirm('Delete this order? This is a soft delete and can\'t be undone.');" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="inline-flex items-center h-10 px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-red-600 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2" />
                        </svg>
                        Delete Order
                    </button>
                </form>
            @endif
            @if($order->order_status === 'confirmed' && ($order->sales_count ?? 0) === 0)
                <a href="{{ route('orders.convert-to-sale', $order) }}"
                   class="inline-flex items-center h-10 px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" pointer-events="none">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Convert to Sale
                </a>
            @endif
            <a href="{{ route('orders.index') }}" 
               class="inline-flex items-center h-10 px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Orders
            </a>
        </div>
    </div>

    <!-- Order Status Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Order Status</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    @if($order->order_status === 'draft') bg-gray-100 text-gray-800
                                    @elseif($order->order_status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($order->order_status === 'confirmed') bg-blue-100 text-blue-800
                                    @elseif($order->order_status === 'processing') bg-purple-100 text-purple-800
                                    @elseif($order->order_status === 'shipped') bg-indigo-100 text-indigo-800
                                    @elseif($order->order_status === 'delivered') bg-green-100 text-green-800
                                    @elseif($order->order_status === 'cancelled') bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($order->order_status) }}
                                </span>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Amount</dt>
                            <dd class="text-lg font-medium text-gray-900">Rs {{ number_format($order->total_amount, 2) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Items</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $order->items->count() }} products</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Order Information -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Order Information</h3>
            
            <dl class="space-y-3">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Order Number</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $order->order_number }}</dd>
                </div>
                
                <div>
                    <dt class="text-sm font-medium text-gray-500">Customer</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        <a href="{{ route('customers.show', $order->customer) }}" class="text-blue-600 hover:text-blue-900">
                            {{ $order->customer->display_name }}
                        </a>
                        <span class="ml-2 text-gray-500">({{ $order->customer->customer_type }})</span>
                    </dd>
                </div>
                
                <div>
                    <dt class="text-sm font-medium text-gray-500">Order Date</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $order->order_date ? \Carbon\Carbon::parse($order->order_date)->format('M d, Y') : 'N/A' }}</dd>
                </div>
                
                @if($order->delivery_date)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Delivery Date</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ \Carbon\Carbon::parse($order->delivery_date)->format('M d, Y') }}</dd>
                </div>
                @endif
                
                @if($order->notes)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Notes</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $order->notes }}</dd>
                </div>
                @endif
                
                <div>
                    <dt class="text-sm font-medium text-gray-500">Created By</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $order->createdBy->name ?? 'N/A' }}</dd>
                </div>
                
                <div>
                    <dt class="text-sm font-medium text-gray-500">Created At</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $order->created_at->format('M d, Y H:i') }}</dd>
                </div>
                
                @if($order->updated_at && $order->updated_at != $order->created_at)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $order->updated_at->format('M d, Y H:i') }}</dd>
                </div>
                @endif
            </dl>
        </div>

        <!-- Customer Information -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Customer Information</h3>
            
            <dl class="space-y-3">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Name</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $order->customer->display_name }}</dd>
                </div>
                
                <div>
                    <dt class="text-sm font-medium text-gray-500">Type</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($order->customer->customer_type) }}</dd>
                </div>
                
                @if($order->customer->email)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        <a href="mailto:{{ $order->customer->email }}" class="text-blue-600 hover:text-blue-900">
                            {{ $order->customer->email }}
                        </a>
                    </dd>
                </div>
                @endif
                
                <div>
                    <dt class="text-sm font-medium text-gray-500">Phone</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $order->customer->primary_phone }}</dd>
                </div>
                
                @if($order->customer->address)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Address</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $order->customer->address }}</dd>
                </div>
                @endif
                
                @if($order->customer->city)
                <div>
                    <dt class="text-sm font-medium text-gray-500">City</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $order->customer->city }}</dd>
                </div>
                @endif
            </dl>
        </div>
    </div>

    <!-- Order Items -->
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Order Items</h3>
        
        @if($order->items->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Discount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Line Total</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($order->items as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $item->product->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $item->product->sku }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $item->product->category->name ?? 'N/A' }}</div>
                                    @if($item->product->brand)
                                        <div class="text-sm text-gray-500">{{ $item->product->brand->name }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->quantity }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rs {{ number_format($item->unit_price, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($item->discount_percentage > 0)
                                        {{ $item->discount_percentage }}%
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    Rs {{ number_format($item->line_total, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-right text-sm font-medium text-gray-900">Subtotal:</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">Rs {{ number_format($order->subtotal, 2) }}</td>
                        </tr>
                        @if($order->discount_amount > 0)
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-right text-sm font-medium text-green-600">Total Discount:</td>
                            <td class="px-6 py-4 text-sm font-medium text-green-600">-Rs {{ number_format($order->discount_amount, 2) }}</td>
                        </tr>
                        @endif
                        <tr class="border-t-2 border-gray-200">
                            <td colspan="5" class="px-6 py-4 text-right text-base font-bold text-gray-900">Total Amount:</td>
                            <td class="px-6 py-4 text-base font-bold text-gray-900">Rs {{ number_format($order->total_amount, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No items</h3>
                <p class="mt-1 text-sm text-gray-500">This order has no items.</p>
            </div>
        @endif
    </div>
</div>
@endsection

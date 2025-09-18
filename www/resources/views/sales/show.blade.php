@extends('layouts.app')

@section('title', 'Sale Details')

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
            <h1 class="text-2xl font-bold text-gray-900">Sale Details</h1>
            <p class="mt-1 text-sm text-gray-500">Sale #{{ $sale->sale_number }}</p>
        </div>
        <div class="flex space-x-3">
            @if($sale->canBeEdited())
                <a href="{{ route('sales.edit', $sale) }}" 
                   class="inline-flex items-center h-10 px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Sale
                </a>
            @endif
            @can('invoices.create')
            <form method="POST" action="{{ route('invoices.create-from-sale', $sale) }}" onsubmit="return confirm('Create invoice from this sale?');">
                @csrf
                <button type="submit" 
                        class="inline-flex items-center h-10 px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-3-3v6m9 1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h7l2 2h7a2 2 0 012 2v8z" />
                    </svg>
                    Create Invoice
                </button>
            </form>
            @endcan
            <a href="{{ route('sales.index') }}" 
               class="inline-flex items-center h-10 px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Sales
            </a>
        </div>
    </div>

    <!-- Sale Status Cards -->
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
                            <dt class="text-sm font-medium text-gray-500 truncate">Sale Status</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    @if($sale->status === 'draft') bg-gray-100 text-gray-800
                                    @elseif($sale->status === 'confirmed') bg-blue-100 text-blue-800
                                    @elseif($sale->status === 'shipped') bg-indigo-100 text-indigo-800
                                    @elseif($sale->status === 'delivered') bg-green-100 text-green-800
                                    @elseif($sale->status === 'cancelled') bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($sale->status) }}
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Payment Status</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    @if($sale->payment_status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($sale->payment_status === 'partial') bg-blue-100 text-blue-800
                                    @elseif($sale->payment_status === 'paid') bg-green-100 text-green-800
                                    @elseif($sale->payment_status === 'overdue') bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($sale->payment_status) }}
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
                            <dd class="text-lg font-medium text-gray-900">Rs {{ number_format($sale->total_amount, 2) }}</dd>
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
                            <dd class="text-lg font-medium text-gray-900">{{ $sale->items->count() }} products</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Sale Information -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Sale Information</h3>
            <dl class="space-y-3">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Sale Number</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $sale->sale_number }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Customer</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        <a href="{{ route('customers.show', $sale->customer) }}" class="text-blue-600 hover:text-blue-900">
                            {{ $sale->customer->display_name }}
                        </a>
                        <span class="ml-2 text-gray-500">({{ $sale->customer->customer_type }})</span>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Sale Date</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $sale->sale_date ? \Carbon\Carbon::parse($sale->sale_date)->format('M d, Y') : 'N/A' }}</dd>
                </div>
                @if($sale->due_date)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Due Date</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ \Carbon\Carbon::parse($sale->due_date)->format('M d, Y') }}</dd>
                </div>
                @endif
                @if($sale->notes)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Notes</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $sale->notes }}</dd>
                </div>
                @endif
                <div>
                    <dt class="text-sm font-medium text-gray-500">Created By</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $sale->createdBy->name ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Created At</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $sale->created_at->format('M d, Y H:i') }}</dd>
                </div>
                @if($sale->updated_at && $sale->updated_at != $sale->created_at)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $sale->updated_at->format('M d, Y H:i') }}</dd>
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
                    <dd class="mt-1 text-sm text-gray-900">{{ $sale->customer->display_name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Type</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($sale->customer->customer_type) }}</dd>
                </div>
                @if($sale->customer->email)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        <a href="mailto:{{ $sale->customer->email }}" class="text-blue-600 hover:text-blue-900">
                            {{ $sale->customer->email }}
                        </a>
                    </dd>
                </div>
                @endif
                <div>
                    <dt class="text-sm font-medium text-gray-500">Phone</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $sale->customer->primary_phone }}</dd>
                </div>
                @if($sale->customer->address)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Address</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $sale->customer->address }}</dd>
                </div>
                @endif
                @if($sale->customer->city)
                <div>
                    <dt class="text-sm font-medium text-gray-500">City</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $sale->customer->city }}</dd>
                </div>
                @endif
            </dl>
        </div>
    </div>

    <!-- Sale Items -->
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Sale Items</h3>
        @if($sale->items->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Discount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Line Total</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($sale->items as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $item->product->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $item->product->sku }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->quantity }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rs {{ number_format($item->unit_price, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($item->discount_percent > 0)
                                        {{ $item->discount_percent }}%
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
                            <td colspan="4" class="px-6 py-4 text-right text-sm font-medium text-gray-900">Subtotal:</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">Rs {{ number_format($sale->subtotal, 2) }}</td>
                        </tr>
                        @if($sale->discount_amount > 0)
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-right text-sm font-medium text-green-600">Total Discount:</td>
                            <td class="px-6 py-4 text-sm font-medium text-green-600">-Rs {{ number_format($sale->discount_amount, 2) }}</td>
                        </tr>
                        @endif
                        <tr class="border-t-2 border-gray-200">
                            <td colspan="4" class="px-6 py-4 text-right text-base font-bold text-gray-900">Total Amount:</td>
                            <td class="px-6 py-4 text-base font-bold text-gray-900">Rs {{ number_format($sale->total_amount, 2) }}</td>
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
                <p class="mt-1 text-sm text-gray-500">This sale has no items.</p>
            </div>
        @endif
    </div>
</div>
@endsection



@extends('layouts.app')

@section('title', 'Customer Details')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <a href="{{ route('customers.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $customer->display_name }}</h1>
                <p class="mt-1 text-sm text-gray-500">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if($customer->customer_type === 'business') bg-green-100 text-green-800
                        @else bg-blue-100 text-blue-800
                        @endif">
                        {{ ucfirst($customer->customer_type) }} Customer
                    </span>
                </p>
            </div>
        </div>
        <div class="flex space-x-3">
            @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('customers.edit'))
            <x-action-button type="edit" :href="route('customers.edit', $customer)">Edit Customer</x-action-button>
            @endif
        </div>
    </div>

    <!-- Customer Information -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Main Customer Info -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Customer Information</h3>
                    
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Customer Name</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $customer->name }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Customer Type</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($customer->customer_type === 'business') bg-green-100 text-green-800
                                    @else bg-blue-100 text-blue-800
                                    @endif">
                                    {{ ucfirst($customer->customer_type) }}
                                </span>
                            </dd>
                        </div>
                        
                        @if($customer->email)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Email</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    <a href="mailto:{{ $customer->email }}" class="text-blue-600 hover:text-blue-900">
                                        {{ $customer->email }}
                                    </a>
                                </dd>
                            </div>
                        @endif
                        
                        @if($customer->phone_number)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Phone Number</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    <a href="tel:{{ $customer->phone_number }}" class="text-blue-600 hover:text-blue-900">
                                        {{ $customer->phone_number }}
                                    </a>
                                </dd>
                            </div>
                        @endif
                        
                        @if($customer->company_name)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Company Name</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $customer->company_name }}</dd>
                            </div>
                        @endif
                        
                        @if($customer->contact_person)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Contact Person</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $customer->contact_person }}</dd>
                            </div>
                        @endif
                        
                        @if($customer->brn)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">BRN</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $customer->brn }}</dd>
                            </div>
                        @endif

                        @if($customer->customer_type === 'business' && $customer->vat)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">VAT Number</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $customer->vat }}</dd>
                            </div>
                        @endif
                        
                        @if($customer->address)
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Address</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $customer->address }}</dd>
                            </div>
                        @endif
                        
                        @if($customer->city || $customer->postal_code)
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Location</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $customer->city }}{{ $customer->postal_code ? ' ' . $customer->postal_code : '' }}{{ $customer->country ? ', ' . $customer->country : '' }}
                                </dd>
                            </div>
                        @endif
                        
                        @if($customer->notes)
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Notes</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $customer->notes }}</dd>
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
                            <dt class="text-sm font-medium text-gray-500">Total Orders</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $customer->orders->count() }}</dd>
                        </div>
                        
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500">Total Sales</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $customer->sales->count() }}</dd>
                        </div>
                        
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500">Total Revenue</dt>
                            <dd class="text-sm font-medium text-gray-900">Rs {{ number_format($customer->sales->sum('total_amount'), 2) }}</dd>
                        </div>
                        
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500">Created</dt>
                            <dd class="text-sm text-gray-900">{{ $customer->created_at->format('M d, Y') }}</dd>
                        </div>
                        
                        @if($customer->updated_at != $customer->created_at)
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                                <dd class="text-sm text-gray-900">{{ $customer->updated_at->format('M d, Y') }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Contact Actions -->
            @if($customer->email || $customer->phone_number)
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Contact</h3>
                        
                        <div class="space-y-3">
                            @if($customer->email)
                                <a href="mailto:{{ $customer->email }}" class="flex items-center justify-center w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    Send Email
                                </a>
                            @endif
                            
                            @if($customer->phone_number)
                                <a href="tel:{{ $customer->phone_number }}" class="flex items-center justify-center w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
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

    <!-- Recent Orders -->
    @if($customer->orders->count() > 0)
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Recent Orders</h3>
                
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order #</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($customer->orders->take(5) as $order)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $order->order_number }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $order->order_date ? \Carbon\Carbon::parse($order->order_date)->format('M d, Y') : 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                            @if($order->order_status === 'draft') bg-gray-100 text-gray-800
                                            @elseif($order->order_status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($order->order_status === 'confirmed') bg-blue-100 text-blue-800
                                            @elseif($order->order_status === 'processing') bg-purple-100 text-purple-800
                                            @elseif($order->order_status === 'shipped') bg-indigo-100 text-indigo-800
                                            @elseif($order->order_status === 'delivered') bg-green-100 text-green-800
                                            @elseif($order->order_status === 'cancelled') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ ucfirst($order->order_status ?? 'Unknown') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        Rs {{ number_format($order->total_amount ?? 0, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="#" class="text-blue-600 hover:text-blue-900">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                @if($customer->orders->count() > 5)
                    <div class="mt-4 text-center">
                        <a href="#" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                            View all {{ $customer->orders->count() }} orders →
                        </a>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Recent Sales -->
    @if($customer->sales->count() > 0)
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Recent Sales</h3>
                
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sale #</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($customer->sales->take(5) as $sale)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $sale->sale_number }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $sale->sale_date ? \Carbon\Carbon::parse($sale->sale_date)->format('M d, Y') : 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                            @if($sale->sale_status === 'draft') bg-gray-100 text-gray-800
                                            @elseif($sale->sale_status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($sale->sale_status === 'completed') bg-green-100 text-green-800
                                            @elseif($sale->sale_status === 'cancelled') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ ucfirst($sale->sale_status ?? 'Unknown') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        Rs {{ number_format($sale->total_amount ?? 0, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="#" class="text-blue-600 hover:text-blue-900">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                @if($customer->sales->count() > 5)
                    <div class="mt-4 text-center">
                        <a href="#" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                            View all {{ $customer->sales->count() }} sales →
                        </a>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection

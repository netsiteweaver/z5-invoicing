@extends('layouts.app')

@section('title', 'Invoice Details')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-md bg-green-50 p-4">
            <div class="flex"><div class="ml-3"><p class="text-sm font-medium text-green-800">{{ session('success') }}</p></div></div>
        </div>
    @endif
    @if(session('error'))
        <div class="rounded-md bg-red-50 p-4">
            <div class="flex"><div class="ml-3"><p class="text-sm font-medium text-red-800">{{ session('error') }}</p></div></div>
        </div>
    @endif

    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Invoice #{{ $invoice->invoice_number }}</h1>
            <p class="mt-1 text-sm text-gray-500">Customer: {{ $invoice->customer->display_name }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('invoices.index') }}" class="inline-flex items-center h-10 px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white">Back to Invoices</a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white overflow-hidden shadow rounded-lg"><div class="p-5"><div class="text-sm text-gray-500">Status</div><div class="text-lg font-medium text-gray-900">{{ ucfirst($invoice->status) }}</div></div></div>
        <div class="bg-white overflow-hidden shadow rounded-lg"><div class="p-5"><div class="text-sm text-gray-500">Payment Status</div><div class="text-lg font-medium text-gray-900">{{ ucfirst($invoice->payment_status) }}</div></div></div>
        <div class="bg-white overflow-hidden shadow rounded-lg"><div class="p-5"><div class="text-sm text-gray-500">Invoice Date</div><div class="text-lg font-medium text-gray-900">{{ $invoice->invoice_date }}</div></div></div>
        <div class="bg-white overflow-hidden shadow rounded-lg"><div class="p-5"><div class="text-sm text-gray-500">Total Amount</div><div class="text-lg font-medium text-gray-900">Rs {{ number_format($invoice->total_amount, 2) }}</div></div></div>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Items</h3>
        @if($invoice->items->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Discount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tax</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Line Total</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($invoice->items as $item)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->product_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->quantity }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rs {{ number_format($item->unit_price, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->discount_percentage }}%</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->tax_percentage }}%</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Rs {{ number_format($item->line_total, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-sm text-gray-500">No items.</div>
        @endif
    </div>
</div>
@endsection


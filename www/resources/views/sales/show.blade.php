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
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-4 sm:space-y-0">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Sale Details</h1>
            <p class="mt-1 text-sm text-gray-500">Sale #{{ $sale->sale_number }}</p>
        </div>
        <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
            @if($sale->canBeEdited())
                <a href="{{ route('sales.edit', $sale) }}" 
                   class="inline-flex items-center justify-center h-10 px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Sale
                </a>
            @endif
            <a href="{{ route('sales.index') }}" 
               class="inline-flex items-center justify-center h-10 px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Sales
            </a>
        </div>
    </div>

    <!-- Sale Status Cards -->
    <div class="grid grid-cols-1 gap-4 sm:gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-4 sm:p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div class="ml-3 sm:ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-xs sm:text-sm font-medium text-gray-500 truncate">Sale Status</dt>
                            <dd class="text-base sm:text-lg font-medium text-gray-900">
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
            <div class="p-4 sm:p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                        </svg>
                    </div>
                    <div class="ml-3 sm:ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-xs sm:text-sm font-medium text-gray-500 truncate">Payment Status</dt>
                            <dd class="text-base sm:text-lg font-medium text-gray-900">
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
            <div class="p-4 sm:p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <div class="ml-3 sm:ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-xs sm:text-sm font-medium text-gray-500 truncate">Total Amount</dt>
                            <dd class="text-base sm:text-lg font-medium text-gray-900">Rs {{ number_format($sale->total_amount, 2) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-4 sm:p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <div class="ml-3 sm:ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-xs sm:text-sm font-medium text-gray-500 truncate">Items</dt>
                            <dd class="text-base sm:text-lg font-medium text-gray-900">{{ $sale->items->count() }} products</dd>
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
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $sale->sale_number }}
                        @if($sale->manual_sale_number)
                            <span class="ml-2 text-gray-500">(Manual: {{ $sale->manual_sale_number }})</span>
                        @endif
                    </dd>
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
    <div class="bg-white shadow rounded-lg p-4 sm:p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Sale Items</h3>
        @if($sale->items->count() > 0)
            @php($sumSubtotal = 0)
            @php($sumDiscount = 0)
            @php($sumVat = 0)
            @php($sumTotal = 0)
            
            <!-- Mobile cards (visible on small screens) -->
            <div class="sm:hidden space-y-4">
                @foreach($sale->items as $item)
                    @php(
                        $qty = (float) ($item->quantity ?? 0)
                    )
                    @php(
                        $unitPrice = (float) ($item->unit_price ?? 0)
                    )
                    @php(
                        $lineSub = $qty * $unitPrice
                    )
                    @php(
                        $discountPct = (float) ($item->discount_percent ?? $item->discount_percentage ?? 0)
                    )
                    @php(
                        $discAmtStored = (float) ($item->discount_amount ?? 0)
                    )
                    @php(
                        $discountAmt = $discAmtStored > 0 ? $discAmtStored : ($lineSub * ($discountPct/100))
                    )
                    @php(
                        $taxPctStored = (float) ($item->tax_percent ?? 0)
                    )
                    @php(
                        $taxPct = $taxPctStored > 0 ? $taxPctStored : ((($item->product->tax_type ?? 'standard') === 'standard') ? 15 : 0)
                    )
                    @php(
                        $taxAmtStored = (float) ($item->tax_amount ?? 0)
                    )
                    @php(
                        $taxAmt = $taxAmtStored > 0 ? $taxAmtStored : (($lineSub - $discountAmt) * ($taxPct/100))
                    )
                    @php(
                        $lineTotal = (float) ($item->line_total ?? ($lineSub - $discountAmt + $taxAmt))
                    )
                    @php($sumSubtotal += $lineSub)
                    @php($sumDiscount += $discountAmt)
                    @php($sumVat += $taxAmt)
                    @php($sumTotal += $lineTotal)
                    
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-900">{{ $item->product->name }}</div>
                                <div class="text-xs text-gray-500">{{ $item->product->sku }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold text-gray-900">Rs {{ number_format($lineTotal, 2) }}</div>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div>
                                <div class="text-xs text-gray-500 mb-1">Quantity</div>
                                <div class="text-sm font-medium text-gray-900">{{ $item->quantity }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500 mb-1">Unit Price</div>
                                <div class="text-sm font-medium text-gray-900">Rs {{ number_format($unitPrice, 2) }}</div>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div>
                                <div class="text-xs text-gray-500 mb-1">Discount</div>
                                <div class="text-sm font-medium text-gray-900">{{ number_format($discountPct, 2) }}%</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500 mb-1">VAT</div>
                                <div class="text-sm font-medium text-gray-900">{{ number_format($taxPct, 0) }}%</div>
                            </div>
                        </div>
                        
                        <div class="border-t pt-2">
                            <div class="flex justify-between text-xs text-gray-500">
                                <span>VAT Amount:</span>
                                <span>Rs {{ number_format($taxAmt, 2) }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
                
                <!-- Mobile Summary -->
                <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal (excl. VAT):</span>
                            <span class="font-medium text-gray-900">Rs {{ number_format($sumSubtotal, 2) }}</span>
                        </div>
                        @if($sumDiscount > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-green-600">Total Discount:</span>
                            <span class="font-medium text-green-600">-Rs {{ number_format($sumDiscount, 2) }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">VAT Total:</span>
                            <span class="font-medium text-gray-900">Rs {{ number_format($sumVat, 2) }}</span>
                        </div>
                        <div class="border-t pt-2">
                            <div class="flex justify-between text-base font-bold">
                                <span class="text-gray-900">Total Amount (incl. VAT):</span>
                                <span class="text-gray-900">Rs {{ number_format($sumTotal, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Desktop table (hidden on small screens) -->
            <div class="hidden sm:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Discount %</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">VAT %</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">VAT</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Line Total</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($sale->items as $item)
                            @php(
                                $qty = (float) ($item->quantity ?? 0)
                            )
                            @php(
                                $unitPrice = (float) ($item->unit_price ?? 0)
                            )
                            @php(
                                $lineSub = $qty * $unitPrice
                            )
                            @php(
                                $discountPct = (float) ($item->discount_percent ?? $item->discount_percentage ?? 0)
                            )
                            @php(
                                $discAmtStored = (float) ($item->discount_amount ?? 0)
                            )
                            @php(
                                $discountAmt = $discAmtStored > 0 ? $discAmtStored : ($lineSub * ($discountPct/100))
                            )
                            @php(
                                $taxPctStored = (float) ($item->tax_percent ?? 0)
                            )
                            @php(
                                $taxPct = $taxPctStored > 0 ? $taxPctStored : ((($item->product->tax_type ?? 'standard') === 'standard') ? 15 : 0)
                            )
                            @php(
                                $taxAmtStored = (float) ($item->tax_amount ?? 0)
                            )
                            @php(
                                $taxAmt = $taxAmtStored > 0 ? $taxAmtStored : (($lineSub - $discountAmt) * ($taxPct/100))
                            )
                            @php(
                                $lineTotal = (float) ($item->line_total ?? ($lineSub - $discountAmt + $taxAmt))
                            )
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $item->product->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $item->product->sku }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->quantity }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rs {{ number_format($unitPrice, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($discountPct, 2) }}%</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($taxPct, 0) }}%</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rs {{ number_format($taxAmt, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Rs {{ number_format($lineTotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-right text-sm font-medium text-gray-900">Subtotal (excl. VAT):</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">Rs {{ number_format($sumSubtotal, 2) }}</td>
                        </tr>
                        @if($sumDiscount > 0)
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-right text-sm font-medium text-green-600">Total Discount:</td>
                            <td class="px-6 py-4 text-sm font-medium text-green-600">-Rs {{ number_format($sumDiscount, 2) }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-right text-sm font-medium text-gray-900">VAT Total:</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">Rs {{ number_format($sumVat, 2) }}</td>
                        </tr>
                        <tr class="border-t-2 border-gray-200">
                            <td colspan="6" class="px-6 py-4 text-right text-base font-bold text-gray-900">Total Amount (incl. VAT):</td>
                            <td class="px-6 py-4 text-base font-bold text-gray-900">Rs {{ number_format($sumTotal, 2) }}</td>
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

    <!-- Payments -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Payments</h3>
            <div class="text-sm text-gray-600">
                <span>Paid: <span class="font-semibold">Rs {{ number_format($sale->paid_amount, 2) }}</span></span>
                <span class="mx-2">|</span>
                <span>Outstanding: <span class="font-semibold">Rs {{ number_format($sale->outstanding_amount, 2) }}</span></span>
            </div>
        </div>

        @if($sale->payments->count() > 0)
            <!-- Mobile cards (visible on small screens) -->
            <div class="sm:hidden space-y-4 mb-6">
                @foreach($sale->payments as $payment)
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <div class="text-sm font-medium text-gray-900">Rs {{ number_format($payment->amount, 2) }}</div>
                            <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}</div>
                        </div>
                        <div>
                            @if($payment->payment_status === 'pending')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                            @elseif($payment->payment_status === 'partial')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Partial</span>
                            @elseif($payment->payment_status === 'paid')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Paid</span>
                            @elseif($payment->payment_status === 'overdue')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">Overdue</span>
                            @elseif($payment->payment_status === 'cancelled')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Cancelled</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <div class="text-xs text-gray-500 mb-1">Method</div>
                            <div class="text-sm font-medium text-gray-900">{{ $payment->payment_method }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 mb-1">Reference</div>
                            <div class="text-sm font-medium text-gray-900">{{ $payment->reference_number ?? '-' }}</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Desktop table (hidden on small screens) -->
            <div class="hidden sm:block overflow-x-auto mb-6">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($sale->payments as $payment)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $payment->payment_method }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Rs {{ number_format($payment->amount, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($payment->payment_status === 'pending')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                                    @elseif($payment->payment_status === 'partial')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Partial</span>
                                    @elseif($payment->payment_status === 'paid')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Paid</span>
                                    @elseif($payment->payment_status === 'overdue')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Overdue</span>
                                    @elseif($payment->payment_status === 'cancelled')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Cancelled</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $payment->reference_number ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-sm text-gray-500 mb-6">No payments recorded yet.</div>
        @endif

        @can('sales.edit')
        <form method="POST" action="{{ route('sales.payments.store', $sale) }}" class="space-y-4">
            @csrf
            
            <!-- Mobile layout -->
            <div class="sm:hidden space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Date</label>
                    <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                    @error('payment_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Method</label>
                    <select name="payment_type_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">Select Payment Method</option>
                        @foreach(($paymentTypes ?? []) as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                    @error('payment_type_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Amount</label>
                    <input type="number" name="amount" step="0.01" min="0.01" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="0.00">
                    @error('amount')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Reference</label>
                    <input type="text" name="reference_number" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="Reference number (optional)">
                    @error('reference_number')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea name="notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="Notes (optional)"></textarea>
                    @error('notes')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                
                <div>
                    <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-3 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-plus mr-2"></i>
                        Record Payment
                    </button>
                </div>
            </div>
            
            <!-- Desktop layout -->
            <div class="hidden sm:block grid grid-cols-1 gap-4 sm:grid-cols-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Date</label>
                    <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    @error('payment_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Method</label>
                    <select name="payment_type_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">Select</option>
                        @foreach(($paymentTypes ?? []) as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                    @error('payment_type_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Amount</label>
                    <input type="number" name="amount" step="0.01" min="0.01" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    @error('amount')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Reference</label>
                    <input type="text" name="reference_number" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Ref # (optional)">
                    @error('reference_number')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="flex items-end">
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Record Payment</button>
                </div>
                <div class="sm:col-span-5">
                    <label class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea name="notes" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Notes (optional)"></textarea>
                    @error('notes')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </form>
        @endcan
    </div>
</div>
@endsection



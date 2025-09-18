@extends('layouts.app')

@section('title', 'Record Payment')
@section('description', 'Enter a payment and apply it to a sale')

@section('content')
<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
	<!-- Sales selector -->
	<div class="lg:col-span-1 bg-white shadow rounded-lg p-6">
		<h3 class="text-lg font-medium text-gray-900 mb-4">Select Sale</h3>
		@if($preselectedSale)
			<div class="p-4 border rounded-md bg-gray-50">
				<div class="text-sm text-gray-500">Sale #</div>
				<div class="text-base font-semibold text-gray-900">{{ $preselectedSale->sale_number }}</div>
				<div class="mt-2 text-sm text-gray-700">{{ $preselectedSale->customer->display_name }}</div>
				<div class="mt-1 text-sm text-gray-500">Total: Rs {{ number_format($preselectedSale->total_amount, 2) }}</div>
				<a href="{{ route('sales.show', $preselectedSale) }}" target="_blank" class="mt-3 inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200">View Sale</a>
			</div>
		@else
			<form method="GET" action="{{ route('payments.create') }}">
				<div class="mb-3">
					<label for="search" class="block text-sm font-medium text-gray-700">Search sales</label>
					<input type="text" name="search" id="search" value="{{ request('search') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Sale #, customer name...">
				</div>
				<button type="submit" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
					<i class="fas fa-search mr-2"></i> Filter
				</button>
			</form>
			<div class="mt-4 divide-y divide-gray-200 border rounded-md">
				@forelse($sales as $sale)
					<div class="p-4 flex items-center justify-between">
						<div>
							<div class="text-sm font-medium text-gray-900">{{ $sale->sale_number }}</div>
							<div class="text-sm text-gray-500">{{ $sale->customer->display_name }}</div>
							<div class="text-xs text-gray-500">Total: Rs {{ number_format($sale->total_amount, 2) }} · Status: {{ ucfirst($sale->payment_status) }}</div>
						</div>
						<a href="{{ route('payments.create', ['sale_id' => $sale->id]) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">Select</a>
					</div>
				@empty
					<div class="p-6 text-center text-sm text-gray-500">No outstanding sales found.</div>
				@endforelse
			</div>
			@if(!$preselectedSale)
				<div class="mt-4">{{ $sales->withQueryString()->links() }}</div>
			@endif
		@endif
	</div>

	<!-- Payment form -->
	<div class="lg:col-span-2 bg-white shadow rounded-lg p-6">
		<h3 class="text-lg font-medium text-gray-900 mb-4">Payment Details</h3>
		@if ($errors->any())
			<div class="mb-4 p-4 rounded-md bg-red-50 text-sm text-red-700">
				<ul class="list-disc list-inside">
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
				</ul>
			</div>
		@endif
		<form method="POST" action="{{ route('payments.store') }}" class="space-y-4">
			@csrf
			<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
				<div>
					<label class="block text-sm font-medium text-gray-700">Sale</label>
					<select name="sale_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
						<option value="">Select sale</option>
						@if($preselectedSale)
							<option value="{{ $preselectedSale->id }}" selected>#{{ $preselectedSale->sale_number }} - {{ $preselectedSale->customer->display_name }}</option>
						@endif
						@foreach(($preselectedSale ? collect() : $sales) as $sale)
							<option value="{{ $sale->id }}" {{ old('sale_id') == $sale->id ? 'selected' : '' }}>#{{ $sale->sale_number }} - {{ $sale->customer->display_name }}</option>
						@endforeach
					</select>
				</div>
				<div>
					<label class="block text-sm font-medium text-gray-700">Payment Date</label>
					<input type="date" name="payment_date" value="{{ old('payment_date', now()->toDateString()) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
				</div>
				<div>
					<label class="block text-sm font-medium text-gray-700">Payment Type/Mode</label>
					<select name="payment_type_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
						<option value="">Select type</option>
						@foreach($paymentTypes as $type)
							<option value="{{ $type->id }}" {{ old('payment_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
						@endforeach
					</select>
				</div>
				<div>
					<label class="block text-sm font-medium text-gray-700">Amount</label>
					<input type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="0.00" required>
				</div>
				<div>
					<label class="block text-sm font-medium text-gray-700">Reference #</label>
					<input type="text" name="reference_number" value="{{ old('reference_number') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="e.g., TXN123, CHQ#...">
				</div>
				<div class="sm:col-span-2">
					<label class="block text-sm font-medium text-gray-700">Payment Notes</label>
					<textarea name="notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Optional notes...">{{ old('notes') }}</textarea>
				</div>
			</div>
			<div class="pt-2">
				<button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
					<i class="fas fa-check mr-2"></i>
					Save Payment
				</button>
			</div>
		</form>
	</div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Payment ' . $payment->payment_number)

@section('content')
<div class="space-y-6">
	<div class="bg-white shadow rounded-lg p-6">
		<h3 class="text-lg font-medium text-gray-900 mb-4">Payment Details</h3>
		<dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
			<div>
				<dt class="text-sm text-gray-500">Payment Number</dt>
				<dd class="text-sm font-medium text-gray-900">{{ $payment->payment_number }}</dd>
			</div>
			<div>
				<dt class="text-sm text-gray-500">Payment Date</dt>
				<dd class="text-sm font-medium text-gray-900">{{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d') : '' }}</dd>
			</div>
			<div>
				<dt class="text-sm text-gray-500">Customer</dt>
				<dd class="text-sm font-medium text-gray-900">{{ $payment->customer?->display_name ?: '—' }}</dd>
			</div>
			<div>
				<dt class="text-sm text-gray-500">Sale</dt>
				<dd class="text-sm font-medium text-blue-700">
					@if($payment->sale)
						<a href="{{ route('sales.show', $payment->sale) }}">#{{ $payment->sale->sale_number }}</a>
					@else
						—
					@endif
				</dd>
			</div>
			<div>
				<dt class="text-sm text-gray-500">Payment Method</dt>
				<dd class="text-sm font-medium text-gray-900">{{ $payment->payment_method }}</dd>
			</div>
			<div>
				<dt class="text-sm text-gray-500">Amount</dt>
				<dd class="text-sm font-medium text-gray-900">Rs {{ number_format($payment->amount, 2) }}</dd>
			</div>
			<div class="sm:col-span-2">
				<dt class="text-sm text-gray-500">Notes</dt>
				<dd class="text-sm text-gray-900 whitespace-pre-line">{{ $payment->notes ?: '—' }}</dd>
			</div>
		</dl>
		<div class="mt-6">
			<a href="{{ route('payments.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-700 bg-white hover:bg-gray-50">
				Back to Payments
			</a>
		</div>
	</div>
</div>
@endsection

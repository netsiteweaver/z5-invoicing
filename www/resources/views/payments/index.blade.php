@extends('layouts.app')

@section('title', 'Payments')
@section('description', 'Browse and manage payments')

@section('actions')
<a href="{{ route('payments.create') }}" class="btn btn-create">
	<i class="btn-icon fa-solid fa-plus"></i>
	Record Payment
</a>
@endsection

@section('content')
<div class="bg-white shadow rounded-lg">
	<div class="px-4 py-5 sm:p-6">
		<div class="overflow-x-auto">
			<table class="min-w-full divide-y divide-gray-200">
				<thead class="bg-gray-50">
					<tr>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Number</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
					</tr>
				</thead>
				<tbody class="bg-white divide-y divide-gray-200">
					@forelse($payments as $payment)
					<tr>
						<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $payment->payment_number }}</td>
						<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d') : '' }}</td>
						<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $payment->customer?->display_name ?: '—' }}</td>
						<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $payment->payment_method }}</td>
						<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Rs {{ number_format($payment->amount, 2) }}</td>
						<td class="px-6 py-4 whitespace-nowrap text-sm">
							<a href="{{ route('payments.show', $payment) }}" class="btn btn-view">
								<i class="btn-icon fa-regular fa-eye"></i>
								View
							</a>
						</td>
					</tr>
					@empty
					<tr>
						<td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">No payments found.</td>
					</tr>
					@endforelse
				</tbody>
			</table>
		</div>
	</div>
	<div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
		{{ $payments->links() }}
	</div>
</div>
@endsection

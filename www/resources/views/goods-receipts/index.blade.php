@extends('layouts.app')

@section('title', 'Goods Receipts')
@section('description', 'List of received goods (GRN)')

@section('actions')
<a href="{{ route('goods-receipts.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
    <i class="fas fa-plus mr-2"></i> New Receipt
</a>
@endsection

@section('content')
<div class="bg-white shadow rounded-lg">
  <div class="px-4 py-5 sm:p-6">
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">GRN #</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          @forelse($receipts as $receipt)
          <tr class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap">{{ $receipt->grn_number }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ optional($receipt->receipt_date instanceof \Carbon\Carbon ? $receipt->receipt_date : \Carbon\Carbon::parse($receipt->receipt_date))->format('Y-m-d') }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ $receipt->department->name ?? '-' }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ $receipt->supplier->name ?? ($receipt->supplier_name ?? '-') }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
              <a href="{{ route('goods-receipts.show', $receipt) }}" class="text-blue-600 hover:text-blue-900"><i class="fas fa-eye"></i></a>
              <a href="{{ route('goods-receipts.edit', $receipt) }}" class="text-yellow-600 hover:text-yellow-900 ml-3"><i class="fas fa-edit"></i></a>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No receipts found.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
    {{ $receipts->links() }}
  </div>
</div>
@endsection

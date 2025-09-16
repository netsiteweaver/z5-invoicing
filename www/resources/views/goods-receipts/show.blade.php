@extends('layouts.app')

@section('title', 'Goods Receipt ' . $receipt->grn_number)

@section('actions')
<div class="flex space-x-2">
  <a href="{{ route('goods-receipts.print', $receipt) }}" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm bg-white hover:bg-gray-50">
    <i class="fas fa-print mr-2"></i> Print
  </a>
  @if(($receipt->approval_status ?? 'approved') !== 'approved')
  <form method="POST" action="{{ route('goods-receipts.approve', $receipt) }}">
    @csrf
    <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent rounded-md text-sm text-white bg-green-600 hover:bg-green-700">
      <i class="fas fa-check mr-2"></i> Approve
    </button>
  </form>
  @endif
</div>
@endsection

@section('content')
<div class="bg-white shadow rounded-lg">
  <div class="px-4 py-5 sm:p-6">
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
      <div>
        <div class="text-xs text-gray-500">GRN Number</div>
        <div class="text-sm font-medium text-gray-900">{{ $receipt->grn_number }}</div>
      </div>
      <div>
        <div class="text-xs text-gray-500">Date</div>
        <div class="text-sm font-medium text-gray-900">{{ $receipt->receipt_date->format('Y-m-d') }}</div>
      </div>
      <div>
        <div class="text-xs text-gray-500">Location</div>
        <div class="text-sm font-medium text-gray-900">{{ $receipt->department->name ?? '-' }}</div>
      </div>
      <div>
        <div class="text-xs text-gray-500">Supplier</div>
        <div class="text-sm font-medium text-gray-900">{{ $receipt->supplier->name ?? ($receipt->supplier_name ?? '-') }}</div>
      </div>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Cost</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          @foreach($receipt->items as $item)
          <tr>
            <td class="px-6 py-4 whitespace-nowrap">{{ $item->product->name ?? ('#'.$item->product_id) }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ $item->quantity }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ $item->unit_cost ? number_format($item->unit_cost, 2) : '-' }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection



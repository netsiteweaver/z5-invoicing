@extends('layouts.app')

@section('title', 'Stock Transfer ' . $transfer->transfer_number)

@section('actions')
<div class="flex space-x-2">
  @if($transfer->status !== 'received')
  <a href="{{ route('stock-transfers.edit', $transfer) }}" class="inline-flex items-center px-3 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-amber-600 hover:bg-amber-700">
    <i class="fas fa-pen mr-2"></i>
    Edit
  </a>
  @endif
  <a href="{{ route('stock-transfers.print', $transfer) }}" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
    <i class="fas fa-print mr-2"></i>
    Print
  </a>
  @if($transfer->status !== 'received')
  <form method="POST" action="{{ route('stock-transfers.receive', $transfer) }}" class="inline">
    @csrf
    <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
      <i class="fas fa-box-open mr-2"></i>
      Mark as Received
    </button>
  </form>
  @endif
</div>
@endsection

@section('content')
<div class="bg-white shadow rounded-lg">
  <div class="px-4 py-5 sm:p-6">
    <div class="grid grid-cols-1 sm:grid-cols-5 gap-4 mb-6">
      <div>
        <div class="text-xs text-gray-500">Transfer #</div>
        <div class="text-sm font-medium text-gray-900">{{ $transfer->transfer_number }}</div>
      </div>
      <div>
        <div class="text-xs text-gray-500">Date</div>
        <div class="text-sm font-medium text-gray-900">{{ optional($transfer->transfer_date instanceof \Carbon\Carbon ? $transfer->transfer_date : \Carbon\Carbon::parse($transfer->transfer_date))->format('Y-m-d') }}</div>

      </div>
      <div>
        <div class="text-xs text-gray-500">From</div>
        <div class="text-sm font-medium text-gray-900">{{ $transfer->fromDepartment->name ?? '-' }}</div>
      </div>
      <div>
        <div class="text-xs text-gray-500">To</div>
        <div class="text-sm font-medium text-gray-900">{{ $transfer->toDepartment->name ?? '-' }}</div>
      </div>
      <div>
        <div class="text-xs text-gray-500">Status</div>
        <div class="text-sm font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $transfer->status)) }}</div>
      </div>
    </div>

    <!-- Desktop Table -->
    <div class="hidden sm:block overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          @foreach($transfer->items as $item)
          <tr>
            <td class="px-6 py-4 whitespace-nowrap">{{ $item->product->name ?? ('#'.$item->product_id) }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ $item->quantity }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    
    <!-- Mobile Cards -->
    <div class="sm:hidden space-y-3">
      @foreach($transfer->items as $item)
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
          <div class="flex items-center justify-between">
            <div class="min-w-0 flex-1">
              <h3 class="text-sm font-medium text-gray-900 truncate">{{ $item->product->name ?? ('#'.$item->product_id) }}</h3>
            </div>
            <div class="ml-2 flex-shrink-0">
              <span class="text-lg font-semibold text-gray-900">{{ $item->quantity }}</span>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</div>
@endsection



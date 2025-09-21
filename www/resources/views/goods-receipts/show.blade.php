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
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Line Total (incl. VAT)</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          @php
            $totalQty = 0;
            $totalNet = 0;
            $totalVat = 0;
            $totalGross = 0;
          @endphp
          @foreach($receipt->items as $item)
            @php
              $unitCost = $item->unit_cost ?? 0;
              $quantity = $item->quantity;
              $grossAmount = $quantity * $unitCost;
              $vatRate = ($item->product->tax_type ?? 'standard') === 'standard' ? 0.15 : 0;
              $vatAmount = $grossAmount * $vatRate;
              $lineTotal = $grossAmount + $vatAmount;
              
              $totalQty += $quantity;
              $totalNet += $grossAmount;
              $totalVat += $vatAmount;
              $totalGross += $lineTotal;
            @endphp
          <tr>
            <td class="px-6 py-4 whitespace-nowrap">{{ $item->product->name ?? ('#'.$item->product_id) }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ $quantity }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ number_format($unitCost, 2) }}</td>
            <td class="px-6 py-4 whitespace-nowrap font-medium">{{ number_format($lineTotal, 2) }}</td>
          </tr>
          @endforeach
        </tbody>
        <tfoot class="bg-gray-50">
          <tr class="font-medium">
            <td class="px-6 py-3 text-left text-sm text-gray-900">Totals:</td>
            <td class="px-6 py-3 text-left text-sm text-gray-900">{{ $totalQty }}</td>
            <td class="px-6 py-3 text-left text-sm text-gray-900">Products (excl. VAT): {{ number_format($totalNet, 2) }}</td>
            <td class="px-6 py-3 text-left text-sm text-gray-900">
              <div class="text-sm">
                <div>VAT (15%): {{ number_format($totalVat, 2) }}</div>
                <div class="font-semibold">Grand Total: {{ number_format($totalGross, 2) }}</div>
              </div>
            </td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>
@endsection



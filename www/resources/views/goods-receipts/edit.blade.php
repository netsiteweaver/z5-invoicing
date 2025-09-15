@extends('layouts.app')

@section('title', 'Edit Goods Receipt ' . $receipt->grn_number)

@section('content')
<form method="POST" action="{{ route('goods-receipts.update', $receipt) }}" class="bg-white shadow rounded-lg">
  @csrf
  @method('PUT')
  <div class="px-4 py-5 sm:p-6 space-y-6">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div>
        <label class="block text-sm font-medium text-gray-700">Receipt Date</label>
        <input type="date" name="receipt_date" value="{{ $receipt->receipt_date->format('Y-m-d') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Supplier</label>
        <input type="text" name="supplier_name" value="{{ $receipt->supplier_name }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Supplier Ref</label>
        <input type="text" name="supplier_ref" value="{{ $receipt->supplier_ref }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
      </div>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700">Notes</label>
      <textarea name="notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ $receipt->notes }}</textarea>
    </div>
  </div>
  <div class="bg-gray-50 px-4 py-3 sm:px-6 text-right">
    <a href="{{ route('goods-receipts.show', $receipt) }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm bg-white hover:bg-gray-50">Cancel</a>
    <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">Save Changes</button>
  </div>
</form>
@endsection



@extends('layouts.app')

@section('title', 'New Stock Transfer')

@section('content')
<form method="POST" action="{{ route('stock-transfers.store') }}" class="bg-white shadow rounded-lg">
  @csrf
  <div class="px-4 py-5 sm:p-6 space-y-6">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div>
        <label class="block text-sm font-medium text-gray-700">From Location</label>
        <select name="from_department_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
          <option value="">Select source</option>
          @foreach($departments as $d)
          <option value="{{ $d->id }}">{{ $d->name }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">To Location</label>
        <select name="to_department_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
          <option value="">Select destination</option>
          @foreach($departments as $d)
          <option value="{{ $d->id }}">{{ $d->name }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Date</label>
        <input type="date" name="transfer_date" value="{{ date('Y-m-d') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
      </div>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700">Notes</label>
      <textarea name="notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
    </div>

    <div>
      <h3 class="text-md font-medium text-gray-900 mb-2">Items</h3>
      <div id="items" class="space-y-3"></div>
      <button type="button" onclick="addItem()" class="mt-2 inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm bg-white hover:bg-gray-50">
        <i class="fas fa-plus mr-2"></i> Add Item
      </button>
    </div>
  </div>

  <div class="bg-gray-50 px-4 py-3 sm:px-6 text-right">
    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
      <i class="fas fa-check mr-2"></i>
      Create Transfer
    </button>
  </div>
</form>

<template id="item-template">
  <div class="grid grid-cols-1 sm:grid-cols-5 gap-3 items-end bg-gray-50 p-3 rounded">
    <div class="sm:col-span-2">
      <label class="block text-sm font-medium text-gray-700">Product</label>
      <select name="items[IDX][product_id]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
        <option value="">Select product</option>
        @foreach($products as $p)
        <option value="{{ $p->id }}">{{ $p->name }}</option>
        @endforeach
      </select>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700">Qty</label>
      <input type="number" name="items[IDX][quantity]" min="1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
    </div>
    <div class="flex space-x-2">
      <button type="button" class="px-3 py-2 border border-gray-300 rounded-md text-sm bg-white hover:bg-gray-50" onclick="removeItem(this)">Remove</button>
    </div>
  </div>
  
</template>

<script>
let itemIndex = 0;
function addItem() {
  const tpl = document.getElementById('item-template').innerHTML.replaceAll('IDX', itemIndex++);
  const wrapper = document.createElement('div');
  wrapper.innerHTML = tpl;
  document.getElementById('items').appendChild(wrapper);
}
function removeItem(btn) {
  const row = btn.closest('.grid');
  row.parentElement.remove();
}
document.addEventListener('DOMContentLoaded', () => addItem());
</script>


@endsection

@extends('layouts.app')

@section('title', 'New Stock Transfer')

@section('content')
<form method="POST" action="{{ route('stock-transfers.store') }}" class="bg-white shadow rounded-lg">
  @csrf
  <div class="px-4 py-5 sm:p-6 space-y-6">
    <!-- Transfer Information Section -->
    <div>
      <h3 class="text-lg font-medium text-gray-900 mb-4">
        <i class="fas fa-info-circle mr-2"></i>
        Transfer Information
      </h3>
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
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
    </div>

    <!-- Additional Details Section -->
    <div>
      <h3 class="text-lg font-medium text-gray-900 mb-4">
        <i class="fas fa-clipboard-list mr-2"></i>
        Additional Details
      </h3>
      <div>
        <label class="block text-sm font-medium text-gray-700">Notes</label>
        <textarea name="notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
      </div>
    </div>

    <!-- Items Section -->
    <div>
      <h3 class="text-lg font-medium text-gray-900 mb-4">
        <i class="fas fa-boxes mr-2"></i>
        Items
      </h3>
      <div id="items" class="space-y-3"></div>
      <button type="button" onclick="addItem()" class="mt-2 w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md text-sm bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
        <i class="fas fa-plus mr-2"></i> Add Item
      </button>
    </div>
  </div>

  <!-- Form Actions -->
  <div class="bg-gray-50 px-4 py-3 sm:px-6">
    <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3">
      <a href="{{ route('stock-transfers.index') }}" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
        Cancel
      </a>
      <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
        <i class="fas fa-check mr-2"></i>
        Create Transfer
      </button>
    </div>
  </div>
</form>

<template id="item-template">
  <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
    <!-- Mobile Layout -->
    <div class="sm:hidden space-y-4">
      <div>
        <label class="block text-sm font-medium text-gray-700">Product</label>
        <select name="items[IDX][product_id]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
          <option value="">Select product</option>
          @foreach($products as $p)
          <option value="{{ $p->id }}">{{ $p->name }}</option>
          @endforeach
        </select>
      </div>
      
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-sm font-medium text-gray-700">Qty</label>
          <input type="number" name="items[IDX][quantity]" min="1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">UOM Qty</label>
          <input type="number" name="items[IDX][uom_quantity]" value="1" min="1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>
      </div>
      
      <div>
        <label class="block text-sm font-medium text-gray-700">UOM</label>
        <select name="items[IDX][uom_id]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
          <option value="">Unit</option>
          @foreach(\App\Models\Uom::orderBy('name')->get() as $u)
          <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->units_per_uom }})</option>
          @endforeach
        </select>
      </div>
      
      <div class="flex justify-end pt-2 border-t border-gray-300">
        <button type="button" class="px-3 py-2 border border-red-300 rounded-md text-sm bg-white hover:bg-red-50 text-red-700" onclick="removeItem(this)">
          <i class="fas fa-trash mr-1"></i>
          Remove
        </button>
      </div>
    </div>
    
    <!-- Desktop Layout -->
    <div class="hidden sm:grid grid-cols-6 gap-3 items-end">
      <div class="col-span-2">
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
      <div>
        <label class="block text-sm font-medium text-gray-700">UOM</label>
        <select name="items[IDX][uom_id]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
          <option value="">Unit</option>
          @foreach(\App\Models\Uom::orderBy('name')->get() as $u)
          <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->units_per_uom }})</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">UOM Qty</label>
        <input type="number" name="items[IDX][uom_quantity]" value="1" min="1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
      </div>
      <div class="flex space-x-2 justify-end">
        <button type="button" class="px-3 py-2 border border-gray-300 rounded-md text-sm bg-white hover:bg-gray-50" onclick="removeItem(this)">Remove</button>
      </div>
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

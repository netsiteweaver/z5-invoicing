@extends('layouts.app')

@section('title', 'New Goods Receipt')
@section('description', 'Record received goods (multiple items)')

@section('content')
<form method="POST" action="{{ route('goods-receipts.store') }}" class="bg-white shadow rounded-lg">
  @csrf
  <div class="px-4 py-5 sm:p-6 space-y-6">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div>
        <label class="block text-sm font-medium text-gray-700">Location</label>
        <select name="department_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
          <option value="">Select location</option>
          @foreach($departments as $dept)
            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Receipt Date</label>
        <input type="date" name="receipt_date" value="{{ date('Y-m-d') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Supplier</label>
        <select name="supplier_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
          <option value="">Select supplier</option>
          @foreach($suppliers as $s)
            <option value="{{ $s->id }}">{{ $s->name }}</option>
          @endforeach
        </select>
      </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div>
        <label class="block text-sm font-medium text-gray-700">Supplier Ref</label>
        <input type="text" name="supplier_ref" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Container No</label>
        <input type="text" name="container_no" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Bill of Lading</label>
        <input type="text" name="bill_of_lading" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
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
    <a href="{{ route('goods-receipts.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm bg-white hover:bg-gray-50">Cancel</a>
    <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">Save Receipt</button>
  </div>
</form>

<template id="item-template">
  <div class="grid grid-cols-1 sm:grid-cols-6 gap-3 items-end bg-gray-50 p-3 rounded">
    <div class="sm:col-span-2">
      <label class="block text-sm font-medium text-gray-700">Product</label>
      <select name="items[IDX][product_id]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm gr-product-select" required>
        <option value="">Select product</option>
        @foreach($products as $p)
          <option value="{{ $p->id }}" data-cost="{{ number_format((float) $p->cost_price, 2, '.', '') }}" data-tax="{{ $p->tax_type }}">{{ $p->name }}</option>
        @endforeach
      </select>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700">Qty</label>
      <input type="number" name="items[IDX][quantity]" min="1" value="1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm gr-qty" required>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700">Unit Cost</label>
      <input type="number" step="0.01" name="items[IDX][unit_cost]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm gr-unit-cost">
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700">UOM</label>
      <input type="text" name="items[IDX][uom]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700">Line Total</label>
      <div class="mt-1 text-gray-900 gr-line-total">0.00</div>
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
  // initialize totals for the newly added row
  const row = wrapper.querySelector('.grid');
  if (row) {
    recalcRow(row);
    recalcTotals();
  }
}
function removeItem(btn) {
  const row = btn.closest('.grid');
  row.parentElement.remove();
  recalcTotals();
}
document.addEventListener('DOMContentLoaded', () => addItem());

// Delegate change handler to populate unit cost from selected product's data-cost
document.addEventListener('change', function(e) {
  if (e.target && e.target.classList.contains('gr-product-select')) {
    const select = e.target;
    const cost = select.selectedOptions[0]?.getAttribute('data-cost');
    const taxType = select.selectedOptions[0]?.getAttribute('data-tax') || 'standard';
    const row = select.closest('.grid');
    if (row) {
      const costInput = row.querySelector('.gr-unit-cost');
      const qtyInput = row.querySelector('.gr-qty');
      if (costInput && cost) {
        costInput.value = cost;
      }
      if (qtyInput) {
        qtyInput.focus();
        qtyInput.select();
      }
      row.setAttribute('data-tax-type', taxType);
      recalcRow(row);
      recalcTotals();
    }
  }
});

// Recalculate line total for a row
function recalcRow(row) {
  const qty = parseFloat((row.querySelector('.gr-qty')?.value || '0')) || 0;
  const cost = parseFloat((row.querySelector('.gr-unit-cost')?.value || '0')) || 0;
  const total = (qty * cost).toFixed(2);
  const totalEl = row.querySelector('.gr-line-total');
  if (totalEl) totalEl.textContent = total;
}

// Recalculate footer totals
function recalcTotals() {
  let sumQty = 0;
  let sumAmount = 0; // gross
  let sumNet = 0;
  let sumVat = 0;
  document.querySelectorAll('#items .grid').forEach(row => {
    const qty = parseFloat((row.querySelector('.gr-qty')?.value || '0')) || 0;
    const cost = parseFloat((row.querySelector('.gr-unit-cost')?.value || '0')) || 0;
    sumQty += qty;
    const gross = qty * cost;
    sumAmount += gross;
    const rate = getVatRateForRow(row);
    const net = rate > 0 ? gross / (1 + rate) : gross;
    sumNet += net;
    sumVat += gross - net;
  });
  const qtyEl = document.getElementById('gr-total-qty');
  const amtEl = document.getElementById('gr-total-amount');
  const netEl = document.getElementById('gr-total-net');
  const vatEl = document.getElementById('gr-total-vat');
  if (qtyEl) qtyEl.textContent = sumQty.toString();
  if (amtEl) amtEl.textContent = sumAmount.toFixed(2);
  if (netEl) netEl.textContent = sumNet.toFixed(2);
  if (vatEl) vatEl.textContent = sumVat.toFixed(2);
}

// Listen for changes in qty and cost inputs
document.addEventListener('input', function(e) {
  if (e.target && (e.target.classList.contains('gr-qty') || e.target.classList.contains('gr-unit-cost'))) {
    const row = e.target.closest('.grid');
    if (row) {
      recalcRow(row);
      recalcTotals();
    }
  }
});

// VAT helpers
const DEFAULT_VAT_RATE = 0.15; // 15% VAT
function getVatRateForRow(row) {
  const taxType = row?.getAttribute('data-tax-type') || row?.querySelector('.gr-product-select')?.selectedOptions[0]?.getAttribute('data-tax') || 'standard';
  if (taxType === 'standard') return DEFAULT_VAT_RATE;
  return 0;
}
</script>


<div class="mt-4 bg-gray-50 border rounded p-3 flex flex-col md:flex-row md:items-center md:justify-end md:space-x-6 space-y-2 md:space-y-0">
  <div class="text-sm text-gray-600">Total Qty: <span id="gr-total-qty" class="font-medium text-gray-900">0</span></div>
  <div class="text-sm text-gray-600">Products (excl. VAT): <span id="gr-total-net" class="font-medium text-gray-900">0.00</span></div>
  <div class="text-sm text-gray-600">Total VAT: <span id="gr-total-vat" class="font-medium text-gray-900">0.00</span></div>
  <div class="text-sm text-gray-600">Grand Total (incl. VAT): <span id="gr-total-amount" class="font-medium text-gray-900">0.00</span></div>
</div>

@endsection

@extends('layouts.app')

@section('title', 'New Goods Receipt')
@section('description', 'Record received goods (multiple items)')

@section('content')
<form method="POST" action="{{ route('goods-receipts.store') }}" class="bg-white shadow rounded-lg">
  @csrf
  <div class="px-4 py-5 sm:p-6 space-y-6">
    <!-- Basic Information Section -->
    <div>
      <h3 class="text-lg font-medium text-gray-900 mb-4">
        <i class="fas fa-info-circle mr-2"></i>
        Basic Information
      </h3>
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div>
          <label class="block text-sm font-medium text-gray-700">Location</label>
          <input type="hidden" name="department_id" value="{{ $defaultDepartment->id }}">
          <input type="text" value="{{ $defaultDepartment->name }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-50" readonly>
          <p class="mt-1 text-sm text-gray-500">Location is set to the main department and cannot be changed</p>
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
    </div>

    <!-- Additional Details Section -->
    <div>
      <h3 class="text-lg font-medium text-gray-900 mb-4">
        <i class="fas fa-clipboard-list mr-2"></i>
        Additional Details
      </h3>
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div>
          <label class="block text-sm font-medium text-gray-700">Supplier Ref</label>
          <input type="text" name="supplier_ref" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>
        <div class="sm:col-span-2">
          <label class="block text-sm font-medium text-gray-700">Notes</label>
          <textarea name="notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
        </div>
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
      <a href="{{ route('goods-receipts.index') }}" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
        Cancel
      </a>
      <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
        <i class="fas fa-save mr-2"></i>
        Save Receipt
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
        <select name="items[IDX][product_id]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm gr-product-select" required>
          <option value="">Select product</option>
          @foreach($products as $p)
            <option value="{{ $p->id }}" data-cost="{{ number_format((float) $p->cost_price, 2, '.', '') }}" data-tax="{{ $p->tax_type }}">{{ $p->name }}</option>
          @endforeach
        </select>
      </div>
      
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-sm font-medium text-gray-700">Qty</label>
          <input type="number" name="items[IDX][quantity]" min="1" value="1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm gr-qty" required>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Unit Cost</label>
          <input type="number" step="0.01" name="items[IDX][unit_cost]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm gr-unit-cost">
        </div>
      </div>
      
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-sm font-medium text-gray-700">UOM</label>
          <select name="items[IDX][uom_id]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            <option value="">Select</option>
            @foreach(\App\Models\Uom::orderBy('name')->get() as $u)
              <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->units_per_uom }})</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">UOM Qty</label>
          <input type="number" name="items[IDX][uom_quantity]" value="1" min="1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>
      </div>
      
      <div class="flex items-center justify-between pt-2 border-t border-gray-300">
        <div>
          <label class="block text-sm font-medium text-gray-700">Line Total</label>
          <div class="text-lg font-semibold text-gray-900 gr-line-total">0.00</div>
        </div>
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
        <select name="items[IDX][uom_id]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
          <option value="">Select</option>
          @foreach(\App\Models\Uom::orderBy('name')->get() as $u)
            <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->units_per_uom }})</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">UOM Qty</label>
        <input type="number" name="items[IDX][uom_quantity]" value="1" min="1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Line Total</label>
        <div class="mt-1 text-gray-900 gr-line-total">0.00</div>
      </div>
      <div class="flex space-x-2">
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

// Recalculate line total for a row (including VAT)
async function recalcRow(row) {
  const qty = parseFloat((row.querySelector('.gr-qty')?.value || '0')) || 0;
  const cost = parseFloat((row.querySelector('.gr-unit-cost')?.value || '0')) || 0;
  const gross = qty * cost;
  const taxType = getTaxTypeForRow(row);
  
  const totalEl = row.querySelector('.gr-line-total');
  if (totalEl) {
    // Use VAT API for accurate calculations
    try {
      const response = await fetch('{{ url("/api/vat/calculate") }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
          amount: gross,
          tax_type: taxType,
          calculation_type: 'exclusive'
        })
      });
      const vatData = await response.json();
      totalEl.textContent = vatData.inclusive_price.toFixed(2);
      totalEl.setAttribute('title', `Gross: ${gross.toFixed(2)} + VAT (${vatData.vat_rate_percent}%): ${vatData.vat_amount.toFixed(2)}`);
    } catch (error) {
      console.error('VAT calculation error:', error);
      // Fallback to manual calculation
      const vatRate = getVatRateForRow(row);
      const vatAmount = gross * vatRate;
      const totalWithVat = gross + vatAmount;
      totalEl.textContent = totalWithVat.toFixed(2);
      totalEl.setAttribute('title', `Gross: ${gross.toFixed(2)} + VAT (${(vatRate * 100).toFixed(1)}%): ${vatAmount.toFixed(2)}`);
    }
  }
}

// Recalculate footer totals
function recalcTotals() {
  let sumQty = 0;
  let sumNet = 0; // products excluding VAT
  let sumVat = 0;
  let sumTotal = 0; // total including VAT
  document.querySelectorAll('#items .grid').forEach(row => {
    const qty = parseFloat((row.querySelector('.gr-qty')?.value || '0')) || 0;
    const cost = parseFloat((row.querySelector('.gr-unit-cost')?.value || '0')) || 0;
    sumQty += qty;
    const gross = qty * cost;
    const vatRate = getVatRateForRow(row);
    const vatAmount = gross * vatRate;
    const netAmount = gross; // cost is already net (excluding VAT)
    
    sumNet += netAmount;
    sumVat += vatAmount;
    sumTotal += gross + vatAmount;
  });
  const qtyEl = document.getElementById('gr-total-qty');
  const amtEl = document.getElementById('gr-total-amount');
  const netEl = document.getElementById('gr-total-net');
  const vatEl = document.getElementById('gr-total-vat');
  if (qtyEl) qtyEl.textContent = sumQty.toString();
  if (amtEl) amtEl.textContent = sumTotal.toFixed(2);
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

function getTaxTypeForRow(row) {
  return row?.getAttribute('data-tax-type') || row?.querySelector('.gr-product-select')?.selectedOptions[0]?.getAttribute('data-tax') || 'standard';
}
</script>


<div class="mt-4 bg-gray-50 border rounded p-3 flex flex-col md:flex-row md:items-center md:justify-end md:space-x-6 space-y-2 md:space-y-0">
  <div class="text-sm text-gray-600">Total Qty: <span id="gr-total-qty" class="font-medium text-gray-900">0</span></div>
  <div class="text-sm text-gray-600">Products (excl. VAT): <span id="gr-total-net" class="font-medium text-gray-900">0.00</span></div>
  <div class="text-sm text-gray-600">Total VAT: <span id="gr-total-vat" class="font-medium text-gray-900">0.00</span></div>
  <div class="text-sm text-gray-600">Grand Total (incl. VAT): <span id="gr-total-amount" class="font-medium text-gray-900">0.00</span></div>
</div>

@endsection

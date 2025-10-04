@extends('layouts.app')

@section('title', 'Create Sale')

@section('content')
<div class="space-y-6" x-data="saleForm()" x-init="addItem()">
	<!-- Header -->
	<div class="flex justify-between items-center">
		<div>
			<h1 class="text-2xl font-bold text-gray-900">Create New Sale</h1>
			<p class="mt-1 text-sm text-gray-500">Create a new sale for a customer</p>
		</div>
		<a href="{{ route('sales.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
			<svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
			</svg>
			Back to Sales
		</a>
	</div>

	<form method="POST" action="{{ route('sales.store') }}" class="space-y-6">
		@csrf
		
		<!-- Sale Information -->
		<div class="bg-white shadow rounded-lg p-6">
			<h3 class="text-lg font-medium text-gray-900 mb-4">Sale Information</h3>
			<div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
				<div>
					<label for="customer_id" class="block text-sm font-medium text-gray-700">Customer <span class="text-red-500">*</span></label>
					<select name="customer_id" id="customer_id" required 
							class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
							x-model="form.customer_id" autofocus>
						<option value="">Select a customer</option>
						@foreach($customers as $customer)
							<option value="{{ $customer->id }}">{{ $customer->display_name }} ({{ $customer->customer_type }})</option>
						@endforeach
					</select>
				</div>
				<div>
					<label for="manual_sale_number" class="block text-sm font-medium text-gray-700">Manual Sale Number</label>
					<input type="text" name="manual_sale_number" id="manual_sale_number" value="{{ old('manual_sale_number') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="If issued manually during downtime">
					@error('manual_sale_number')
						<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
					@enderror
				</div>
				<div>
					<label for="sale_date" class="block text-sm font-medium text-gray-700">Sale Date <span class="text-red-500">*</span></label>
					<input type="date" name="sale_date" id="sale_date" required 
						   value="{{ old('sale_date', date('Y-m-d')) }}"
						   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
						   x-model="form.sale_date" @change="recalcDueDate()">
				</div>
				<div>
					<label class="block text-sm font-medium text-gray-700">Payment Terms</label>
					<select name="payment_term_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" x-model="form.payment_term_id" @change="recalcDueDate()">
						<option value="">Manual</option>
						@foreach($paymentTerms as $term)
							<option value="{{ $term->id }}" {{ $term->is_default ? 'selected' : '' }}>{{ $term->name }}</option>
						@endforeach
					</select>
				</div>
				<div>
					<label for="due_date" class="block text-sm font-medium text-gray-700">Due Date</label>
					<input type="date" name="due_date" id="due_date" 
						   value="{{ old('due_date') }}"
						   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
						   x-model="form.due_date">
					<p class="mt-1 text-xs text-gray-500">Auto-calculated from terms unless Manual is selected.</p>
				</div>
				<div class="sm:col-span-2">
					<label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
					<textarea name="notes" id="notes" rows="3" 
								class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
								placeholder="Additional notes for this sale..."></textarea>
				</div>
			</div>
		</div>

		<!-- Sale Items -->
		<div class="bg-white shadow rounded-lg p-6">
			<div class="flex justify-between items-center mb-4">
				<h3 class="text-lg font-medium text-gray-900">Sale Items</h3>
				<button type="button" @click="addItem()" 
						class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
					<svg class="-ml-0.5 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
					</svg>
					Add Item
				</button>
			</div>

			<div class="overflow-x-auto" x-show="form.items.length > 0">
				<table class="min-w-full divide-y divide-gray-200">
					<thead class="bg-gray-50">
						<tr>
							<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">UOM</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">UOM Qty</th>
							<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
							<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Discount %</th>
							<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">VAT</th>
							<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Line Total</th>
							<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
						</tr>
					</thead>
					<tbody class="bg-white divide-y divide-gray-200">
						<template x-for="(item, index) in form.items" :key="index">
							<tr>
								<td class="px-6 py-4 whitespace-nowrap">
									<select :name="`items[${index}][product_id]`" 
											x-model="item.product_id"
											@change="updateItem(index)"
											class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
										<option value="">Select Product</option>
										@foreach($products as $product)
											<option value="{{ $product->id }}" 
													data-price="{{ $product->selling_price }}"
													data-tax-type="{{ $product->tax_type }}">
											{{ $product->name }} - Rs {{ number_format($product->selling_price, 2) }}
											</option>
										@endforeach
									</select>
								</td>
                                <td class="px-6 py-4 whitespace-nowrap">
									<input type="number" :name="`items[${index}][quantity]`" x-model="item.quantity" @input="updateItem(index)" min="1" class="block w-20 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
								</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <select :name="`items[${index}][uom_id]`" x-model="item.uom_id" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        <option value="">Unit</option>
                                        @foreach(\App\Models\Uom::orderBy('name')->get() as $u)
                                            <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->units_per_uom }})</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="number" :name="`items[${index}][uom_quantity]`" x-model="item.uom_quantity" min="1" class="block w-20 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </td>
								<td class="px-6 py-4 whitespace-nowrap">
									<input type="number" :name="`items[${index}][unit_price]`" x-model="item.unit_price" @input="updateItem(index)" step="0.01" min="0" class="block w-24 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
								</td>
								<td class="px-6 py-4 whitespace-nowrap">
									<input type="number" :name="`items[${index}][discount_percentage]`" x-model="item.discount_percentage" @input="updateItem(index)" step="0.01" min="0" max="100" class="block w-20 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
								</td>
								<td class="px-6 py-4 whitespace-nowrap">
									<input type="text" :value="(item.tax_type === 'standard' ? '15%' : (item.tax_type === 'zero' ? '0%' : 'EX'))" class="block w-full border-gray-200 rounded-md shadow-sm bg-gray-50 text-gray-700 sm:text-sm" disabled>
								</td>
								<td class="px-6 py-4 whitespace-nowrap">
									<span class="text-sm font-medium text-gray-900" x-text="formatCurrency(item.line_total)"></span>
								</td>
								<td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
									<button type="button" @click="removeItem(index)" class="text-red-600 hover:text-red-900">Remove</button>
								</td>
							</tr>
						</template>
					</tbody>
				</table>
			</div>

			<div x-show="form.items.length === 0" class="text-center py-8">
				<svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
				</svg>
				<h3 class="mt-2 text-sm font-medium text-gray-900">No items added</h3>
				<p class="mt-1 text-sm text-gray-500">Add products to this sale to get started.</p>
			</div>
		</div>

		<!-- Sale Summary -->
		<div class="bg-white shadow rounded-lg p-6" x-show="form.items.length > 0">
			<h3 class="text-lg font-medium text-gray-900 mb-4">Sale Summary</h3>
			<div class="space-y-2">
				<div class="flex justify-between">
					<span class="text-sm text-gray-600">Subtotal:</span>
					<span class="text-sm font-medium text-gray-900" x-text="formatCurrency(form.subtotal)"></span>
				</div>
				<div class="flex justify-between">
					<span class="text-sm text-gray-600">Total Discount:</span>
					<span class="text-sm font-medium text-green-600" x-text="formatCurrency(form.total_discount)"></span>
				</div>
				<div class="flex justify-between">
					<span class="text-sm text-gray-600">VAT Total:</span>
					<span class="text-sm font-medium text-gray-900" x-text="formatCurrency(form.total_tax)"></span>
				</div>
				<div class="border-t pt-2">
					<div class="flex justify-between">
						<span class="text-base font-medium text-gray-900">Total Amount (incl. VAT):</span>
						<span class="text-base font-bold text-gray-900" x-text="formatCurrency(form.total_amount)"></span>
					</div>
				</div>
			</div>
		</div>

		<!-- Actions -->
		<div class="flex justify-end space-x-3">
			<a href="{{ route('sales.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Cancel</a>
			<button type="submit" :disabled="form.items.length === 0" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
				Create Sale
			</button>
		</div>
	</form>
</div>

<script>
function saleForm() {
	return {
		form: { customer_id: '', sale_date: '{{ date('Y-m-d') }}', payment_term_id: '{{ optional($paymentTerms->firstWhere('is_default', true))->id }}', due_date: '', items: [], subtotal: 0, total_discount: 0, total_tax: 0, total_amount: 0 },
		addItem() { this.form.items.push({ product_id: '', quantity: 1, unit_price: 0, discount_percentage: 0, tax_type: 'standard', line_total: 0 }); },
        async removeItem(index) { this.form.items.splice(index, 1); await this.calculateTotals(); },
		async updateItem(index) {
			const item = this.form.items[index];
			const sel = document.querySelector(`select[name="items[${index}][product_id]"]`);
			if (sel && sel.selectedOptions[0]) {
				const opt = sel.selectedOptions[0];
				item.unit_price = parseFloat(opt.dataset.price) || 0;
				item.tax_type = opt.dataset.taxType || 'standard';
			}
			const qty = parseFloat(item.quantity) || 0;
			const price = parseFloat(item.unit_price) || 0;
			const discPct = parseFloat(item.discount_percentage) || 0;
			const lineSub = qty * price;
			const discAmt = lineSub * (discPct / 100);
			const netAmount = lineSub - discAmt;
			
			// Use VAT API for accurate calculations
			try {
				const response = await fetch('{{ url("/api/vat/calculate") }}', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
						'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
					},
					body: JSON.stringify({
						amount: netAmount,
						tax_type: item.tax_type,
						calculation_type: 'exclusive'
					})
				});
				const vatData = await response.json();
				item.line_total = vatData.inclusive_price;
			} catch (error) {
				console.error('VAT calculation error:', error);
				// Fallback to manual calculation
				const taxPct = (item.tax_type === 'standard') ? 15 : 0;
				const taxAmt = netAmount * (taxPct / 100);
				item.line_total = netAmount + taxAmt;
			}
			this.calculateTotals();
		},
		calculateTotals() {
			this.form.subtotal = this.form.items.reduce((s, it) => s + ((parseFloat(it.quantity)||0) * (parseFloat(it.unit_price)||0)), 0);
			this.form.total_discount = this.form.items.reduce((s, it) => {
				const qty = parseFloat(it.quantity)||0; const price = parseFloat(it.unit_price)||0; const pct = parseFloat(it.discount_percentage)||0; return s + ((qty*price)*(pct/100));
			}, 0);
			this.form.total_tax = this.form.items.reduce((s, it) => {
				const qty = parseFloat(it.quantity)||0; const price = parseFloat(it.unit_price)||0; const pct = parseFloat(it.discount_percentage)||0; const taxPct = (it.tax_type==='standard')?15:0; const lineSub = qty*price; const discAmt = lineSub*(pct/100); return s + ((lineSub - discAmt)*(taxPct/100));
			}, 0);
			this.form.total_amount = this.form.subtotal - this.form.total_discount + this.form.total_tax;
		},
		recalcDueDate() {
			// If no term selected, keep manual value
			if (!this.form.payment_term_id) return;
			const saleDate = this.form.sale_date || '{{ date('Y-m-d') }}';
			// We infer by server-provided defaults name; client-only quick rules for common terms
			// For robust behavior, server computes again on save
			const selected = document.querySelector('select[name="payment_term_id"]');
			const label = selected ? selected.options[selected.selectedIndex].text.toLowerCase() : '';
			let d = new Date(saleDate + 'T00:00:00');
			if (label.includes('14')) { d.setDate(d.getDate() + 14); }
			else if (label.includes('30')) { d.setDate(d.getDate() + 30); }
			else if (label.includes('60')) { d.setDate(d.getDate() + 60); }
			else if (label.includes('eom')) { d = new Date(d.getFullYear(), d.getMonth() + 1, 0); }
			else { return; }
			const yyyy = d.getFullYear();
			const mm = String(d.getMonth()+1).padStart(2,'0');
			const dd = String(d.getDate()).padStart(2,'0');
			this.form.due_date = `${yyyy}-${mm}-${dd}`;
		},
		formatCurrency(a){ return 'Rs ' + (parseFloat(a||0).toFixed(2)); }
	};
}
</script>
@endsection

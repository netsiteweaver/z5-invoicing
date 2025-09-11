@extends('layouts.app')

@section('title', 'Create Order')

@section('content')
<div class="space-y-6" x-data="orderForm()">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Create New Order</h1>
            <p class="mt-1 text-sm text-gray-500">Create a new order for a customer</p>
        </div>
        <a href="{{ route('orders.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Orders
        </a>
    </div>

    <form method="POST" action="{{ route('orders.store') }}" class="space-y-6">
        @csrf
        
        <!-- Order Information -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Order Information</h3>
            
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <!-- Customer Selection -->
                <div>
                    <label for="customer_id" class="block text-sm font-medium text-gray-700">Customer <span class="text-red-500">*</span></label>
                    <select name="customer_id" id="customer_id" required 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            x-model="form.customer_id"
                            @change="updateCustomerInfo()">
                        <option value="">Select a customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" 
                                    data-type="{{ $customer->customer_type }}"
                                    data-name="{{ $customer->display_name }}"
                                    data-email="{{ $customer->email }}"
                                    data-phone="{{ $customer->primary_phone }}">
                                {{ $customer->display_name }} ({{ $customer->customer_type }})
                            </option>
                        @endforeach
                    </select>
                    @error('customer_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Order Date -->
                <div>
                    <label for="order_date" class="block text-sm font-medium text-gray-700">Order Date <span class="text-red-500">*</span></label>
                    <input type="date" name="order_date" id="order_date" required 
                           value="{{ old('order_date', date('Y-m-d')) }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    @error('order_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Delivery Date -->
                <div>
                    <label for="delivery_date" class="block text-sm font-medium text-gray-700">Delivery Date</label>
                    <input type="date" name="delivery_date" id="delivery_date" 
                           value="{{ old('delivery_date') }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    @error('delivery_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Order Status -->
                <div>
                    <label for="order_status" class="block text-sm font-medium text-gray-700">Order Status <span class="text-red-500">*</span></label>
                    <select name="order_status" id="order_status" required 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="draft" {{ old('order_status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="pending" {{ old('order_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ old('order_status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    </select>
                    @error('order_status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Payment Status -->
                <div>
                    <label for="payment_status" class="block text-sm font-medium text-gray-700">Payment Status <span class="text-red-500">*</span></label>
                    <select name="payment_status" id="payment_status" required 
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="pending" {{ old('payment_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="partial" {{ old('payment_status') === 'partial' ? 'selected' : '' }}>Partial</option>
                        <option value="paid" {{ old('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
                    </select>
                    @error('payment_status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Notes -->
                <div class="sm:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea name="notes" id="notes" rows="3" 
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                              placeholder="Additional notes for this order...">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Order Items</h3>
                <button type="button" @click="addItem()" 
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="-ml-0.5 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Add Item
                </button>
            </div>

            <!-- Items Table -->
            <div class="overflow-x-auto" x-show="form.items.length > 0">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Discount %</th>
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
                                                    data-stock="{{ $product->inventory->sum('current_stock') ?? 0 }}">
                                                {{ $product->name }} - Rs {{ number_format($product->selling_price, 2) }}
                                                @if($product->inventory->sum('current_stock') ?? 0 > 0)
                                                    (Stock: {{ $product->inventory->sum('current_stock') }})
                                                @else
                                                    (Out of Stock)
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="number" 
                                           :name="`items[${index}][quantity]`"
                                           x-model="item.quantity"
                                           @input="updateItem(index)"
                                           min="1"
                                           class="block w-20 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="number" 
                                           :name="`items[${index}][unit_price]`"
                                           x-model="item.unit_price"
                                           @input="updateItem(index)"
                                           step="0.01"
                                           min="0"
                                           class="block w-24 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="number" 
                                           :name="`items[${index}][discount_percentage]`"
                                           x-model="item.discount_percentage"
                                           @input="updateItem(index)"
                                           step="0.01"
                                           min="0"
                                           max="100"
                                           class="block w-20 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-medium text-gray-900" x-text="formatCurrency(item.line_total)"></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button type="button" @click="removeItem(index)" 
                                            class="text-red-600 hover:text-red-900">
                                        Remove
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Empty State -->
            <div x-show="form.items.length === 0" class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No items added</h3>
                <p class="mt-1 text-sm text-gray-500">Add products to this order to get started.</p>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="bg-white shadow rounded-lg p-6" x-show="form.items.length > 0">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Order Summary</h3>
            
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Subtotal:</span>
                    <span class="text-sm font-medium text-gray-900" x-text="formatCurrency(form.subtotal)"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Total Discount:</span>
                    <span class="text-sm font-medium text-green-600" x-text="formatCurrency(form.total_discount)"></span>
                </div>
                <div class="border-t pt-2">
                    <div class="flex justify-between">
                        <span class="text-base font-medium text-gray-900">Total Amount:</span>
                        <span class="text-base font-bold text-gray-900" x-text="formatCurrency(form.total_amount)"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('orders.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Cancel
            </a>
            <button type="submit" 
                    :disabled="form.items.length === 0"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                Create Order
            </button>
        </div>
    </form>
</div>

<script>
function orderForm() {
    return {
        form: {
            customer_id: '',
            items: [],
            subtotal: 0,
            total_discount: 0,
            total_amount: 0
        },

        addItem() {
            this.form.items.push({
                product_id: '',
                quantity: 1,
                unit_price: 0,
                discount_percentage: 0,
                line_total: 0
            });
        },

        removeItem(index) {
            this.form.items.splice(index, 1);
            this.calculateTotals();
        },

        updateItem(index) {
            const item = this.form.items[index];
            const productSelect = document.querySelector(`select[name="items[${index}][product_id]"]`);
            
            if (productSelect && productSelect.selectedOptions[0]) {
                const selectedOption = productSelect.selectedOptions[0];
                const price = parseFloat(selectedOption.dataset.price) || 0;
                item.unit_price = price;
            }

            const quantity = parseFloat(item.quantity) || 0;
            const unitPrice = parseFloat(item.unit_price) || 0;
            const discountPercent = parseFloat(item.discount_percentage) || 0;
            
            const lineSubtotal = quantity * unitPrice;
            const discountAmount = lineSubtotal * (discountPercent / 100);
            item.line_total = lineSubtotal - discountAmount;
            
            this.calculateTotals();
        },

        calculateTotals() {
            this.form.subtotal = this.form.items.reduce((sum, item) => {
                const quantity = parseFloat(item.quantity) || 0;
                const unitPrice = parseFloat(item.unit_price) || 0;
                return sum + (quantity * unitPrice);
            }, 0);

            this.form.total_discount = this.form.items.reduce((sum, item) => {
                const quantity = parseFloat(item.quantity) || 0;
                const unitPrice = parseFloat(item.unit_price) || 0;
                const discountPercent = parseFloat(item.discount_percentage) || 0;
                const lineSubtotal = quantity * unitPrice;
                return sum + (lineSubtotal * (discountPercent / 100));
            }, 0);

            this.form.total_amount = this.form.subtotal - this.form.total_discount;
        },

        updateCustomerInfo() {
            // This could be used to populate customer-specific information
            // For now, we'll keep it simple
        },

        formatCurrency(amount) {
            return 'Rs ' + parseFloat(amount || 0).toFixed(2);
        }
    }
}
</script>
@endsection

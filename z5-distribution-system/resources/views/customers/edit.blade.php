@extends('layouts.app')

@section('title', 'Edit Customer')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center">
        <a href="{{ route('customers.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Customer</h1>
            <p class="mt-1 text-sm text-gray-500">Update customer information</p>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white shadow rounded-lg">
        <form method="POST" action="{{ route('customers.update', $customer) }}" class="space-y-6 p-6">
            @csrf
            @method('PUT')
            
            <!-- Customer Type -->
            <div>
                <label for="customer_type" class="block text-sm font-medium text-gray-700">Customer Type *</label>
                <div class="mt-2 space-y-2">
                    <div class="flex items-center">
                        <input id="individual" name="customer_type" type="radio" value="individual" 
                               class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300" 
                               {{ old('customer_type', $customer->customer_type) === 'individual' ? 'checked' : '' }}>
                        <label for="individual" class="ml-3 block text-sm font-medium text-gray-700">
                            Individual
                        </label>
                    </div>
                    <div class="flex items-center">
                        <input id="business" name="customer_type" type="radio" value="business" 
                               class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300"
                               {{ old('customer_type', $customer->customer_type) === 'business' ? 'checked' : '' }}>
                        <label for="business" class="ml-3 block text-sm font-medium text-gray-700">
                            Business
                        </label>
                    </div>
                </div>
                @error('customer_type')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Name / Full Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Name *</label>
                <input type="text" name="full_name" id="full_name" value="{{ old('full_name', $customer->full_name) }}" 
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('name') border-red-300 @enderror" 
                       placeholder="Enter customer name">
                @error('full_name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email', $customer->email) }}" 
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('email') border-red-300 @enderror" 
                       placeholder="customer@example.com">
                @error('email')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Phone Number -->
            <div>
                <label for="phone_number" class="block text-sm font-medium text-gray-700">Phone Number</label>
                <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number', $customer->phone_number) }}" 
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('phone_number') border-red-300 @enderror" 
                       placeholder="(230) 123-4567">
                @error('phone_number')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Company Name (for business customers) -->
            <div id="company-field" style="display: none;">
                <label for="company_name" class="block text-sm font-medium text-gray-700">Company Name</label>
                <input type="text" name="company_name" id="company_name" value="{{ old('company_name', $customer->company_name) }}" 
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('company_name') border-red-300 @enderror" 
                       placeholder="Enter company name">
                @error('company_name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Contact Person (for business customers) -->
            <div id="contact-person-field" style="display: none;">
                <label for="contact_person" class="block text-sm font-medium text-gray-700">Contact Person</label>
                <input type="text" name="contact_person" id="contact_person" value="{{ old('contact_person', $customer->contact_person) }}" 
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('contact_person') border-red-300 @enderror" 
                       placeholder="Enter contact person name">
                @error('contact_person')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Address -->
            <div>
                <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                <textarea name="address" id="address" rows="3" 
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('address') border-red-300 @enderror" 
                          placeholder="Enter full address">{{ old('address', $customer->address) }}</textarea>
                @error('address')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- City, Postal Code -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                    <input type="text" name="city" id="city" value="{{ old('city', $customer->city) }}" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('city') border-red-300 @enderror" 
                           placeholder="City">
                    @error('city')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="postal_code" class="block text-sm font-medium text-gray-700">Postal Code</label>
                    <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', $customer->postal_code) }}" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('postal_code') border-red-300 @enderror" 
                           placeholder="12345">
                    @error('postal_code')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Country -->
            <div>
                <label for="country" class="block text-sm font-medium text-gray-700">Country</label>
                <input type="text" name="country" id="country" value="{{ old('country', $customer->country) }}" 
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('country') border-red-300 @enderror" 
                       placeholder="Country">
                @error('country')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- BRN -->
            <div>
                <label for="brn" class="block text-sm font-medium text-gray-700">BRN</label>
                <input type="text" name="brn" id="brn" value="{{ old('brn', $customer->brn) }}" 
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('brn') border-red-300 @enderror" 
                       placeholder="Business Registration Number">
                @error('brn')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- VAT Number (for business customers) -->
            <div id="vat-field" style="display: none;">
                <label for="vat" class="block text-sm font-medium text-gray-700">VAT Number</label>
                <input type="text" name="vat" id="vat" value="{{ old('vat', $customer->vat) }}" 
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('vat') border-red-300 @enderror" 
                       placeholder="VAT Number">
                @error('vat')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Notes -->
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                <textarea name="notes" id="notes" rows="3" 
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('notes') border-red-300 @enderror" 
                          placeholder="Additional notes about the customer">{{ old('notes', $customer->notes) }}</textarea>
                @error('notes')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('customers.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Update Customer
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const customerTypeRadios = document.querySelectorAll('input[name="customer_type"]');
    const companyField = document.getElementById('company-field');
    const contactPersonField = document.getElementById('contact-person-field');
    const vatField = document.getElementById('vat-field');
    
    function toggleBusinessFields() {
        const isBusiness = document.querySelector('input[name="customer_type"]:checked')?.value === 'business';
        companyField.style.display = isBusiness ? 'block' : 'none';
        contactPersonField.style.display = isBusiness ? 'block' : 'none';
        vatField.style.display = isBusiness ? 'block' : 'none';
    }
    
    customerTypeRadios.forEach(radio => {
        radio.addEventListener('change', toggleBusinessFields);
    });
    
    // Initialize on page load
    toggleBusinessFields();
});
</script>
@endsection

@extends('layouts.app')

@section('title', 'New Supplier')

@section('content')
<form method="POST" action="{{ route('suppliers.store') }}" class="bg-white shadow rounded-lg">
  @csrf
  <div class="px-4 py-5 sm:p-6 space-y-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium text-gray-700">Name</label>
        <input type="text" name="name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Contact Person</label>
        <input type="text" name="contact_person" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Email</label>
        <input type="email" name="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Phone</label>
        <input type="text" name="phone_number1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Alt Phone</label>
        <input type="text" name="phone_number2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Address</label>
        <input type="text" name="address" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">City</label>
        <input type="text" name="city" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Country</label>
        <input type="text" name="country" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
      </div>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700">Remarks</label>
      <textarea name="remarks" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
    </div>
  </div>
  <div class="bg-gray-50 px-4 py-3 sm:px-6 text-right">
    <a href="{{ route('suppliers.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm bg-white hover:bg-gray-50">Cancel</a>
    <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">Save Supplier</button>
  </div>
</form>
@endsection


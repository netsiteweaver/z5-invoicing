@extends('layouts.app')

@section('title', $supplier->name)

@section('actions')
<a href="{{ route('suppliers.edit', $supplier) }}" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm bg-white hover:bg-gray-50">
  <i class="fas fa-edit mr-2"></i> Edit
</a>
@endsection

@section('content')
<div class="bg-white shadow rounded-lg">
  <div class="px-4 py-5 sm:p-6">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
      <div>
        <div class="text-xs text-gray-500">Name</div>
        <div class="text-sm font-medium text-gray-900">{{ $supplier->name }}</div>
      </div>
      <div>
        <div class="text-xs text-gray-500">Contact</div>
        <div class="text-sm font-medium text-gray-900">{{ $supplier->contact_person ?? '-' }}</div>
      </div>
      <div>
        <div class="text-xs text-gray-500">Email</div>
        <div class="text-sm font-medium text-gray-900">{{ $supplier->email ?? '-' }}</div>
      </div>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div>
        <div class="text-xs text-gray-500">Phone</div>
        <div class="text-sm font-medium text-gray-900">{{ $supplier->phone_number1 ?? '-' }}</div>
      </div>
      <div>
        <div class="text-xs text-gray-500">Alt Phone</div>
        <div class="text-sm font-medium text-gray-900">{{ $supplier->phone_number2 ?? '-' }}</div>
      </div>
      <div>
        <div class="text-xs text-gray-500">Location</div>
        <div class="text-sm font-medium text-gray-900">{{ $supplier->city ? ($supplier->city . ', ') : '' }}{{ $supplier->country ?? '' }}</div>
      </div>
    </div>
    @if($supplier->remarks)
    <div class="mt-6">
      <div class="text-xs text-gray-500">Remarks</div>
      <div class="text-sm text-gray-800">{{ $supplier->remarks }}</div>
    </div>
    @endif
  </div>
</div>
@endsection


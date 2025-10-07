@extends('layouts.app')

@section('title', 'Create Payment Term')

@section('content')
<div class="bg-white shadow rounded-lg p-6">
    <form method="POST" action="{{ route('payment-terms.store') }}" class="space-y-4">
        @csrf
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" name="name" value="{{ old('name') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Type</label>
                <select name="type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                    <option value="days">Days</option>
                    <option value="eom">End of Month</option>
                    <option value="manual">Manual</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Days (if type is Days)</label>
                <input type="number" name="days" min="0" value="{{ old('days') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Default</label>
                <input type="checkbox" name="is_default" value="1" class="mt-2">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Description</label>
                <input type="text" name="description" value="{{ old('description') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
        </div>
        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
            <x-action-button type="cancel" :href="route('payment-terms.index')" />
            <x-action-button type="save" />
        </div>
    </form>
</div>
@endsection




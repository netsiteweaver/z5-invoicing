@extends('layouts.app')

@section('title', 'Add UOM')

@section('content')
<div class="bg-white shadow rounded-lg">
    <form method="POST" action="{{ route('uoms.store') }}" class="space-y-6 p-6">
        @csrf
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-gray-700">Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Code <span class="text-red-500">*</span></label>
                <input type="text" name="code" value="{{ old('code') }}" id="code" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required oninput="formatCode(this)">
                @error('code')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Dimension <span class="text-red-500">*</span></label>
                <select name="dimension_code" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                    <option value="count" {{ old('dimension_code') == 'count' ? 'selected' : '' }}>Count</option>
                    <option value="weight" {{ old('dimension_code') == 'weight' ? 'selected' : '' }}>Weight</option>
                    <option value="volume" {{ old('dimension_code') == 'volume' ? 'selected' : '' }}>Volume</option>
                    <option value="length" {{ old('dimension_code') == 'length' ? 'selected' : '' }}>Length</option>
                </select>
                @error('dimension_code')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Units per UOM <span class="text-red-500">*</span></label>
                <input type="number" name="units_per_uom" value="{{ old('units_per_uom', 1) }}" min="1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                @error('units_per_uom')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Factor to Base <span class="text-red-500">*</span></label>
                <input type="number" name="factor_to_base" value="{{ old('factor_to_base', 1) }}" step="0.000001" min="0.000001" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                <p class="mt-1 text-xs text-gray-500">Conversion factor to base unit (e.g., 1000 for kg to g)</p>
                @error('factor_to_base')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Offset to Base</label>
                <input type="number" name="offset_to_base" value="{{ old('offset_to_base', 0) }}" step="0.000001" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <p class="mt-1 text-xs text-gray-500">Offset value (usually 0 for most units)</p>
                @error('offset_to_base')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Minimum Increment</label>
                <input type="number" name="min_increment" value="{{ old('min_increment') }}" step="0.000001" min="0.000001" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <p class="mt-1 text-xs text-gray-500">Minimum increment for this unit (e.g., 0.01 for currency)</p>
                @error('min_increment')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('status', 1) == 0 ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ old('description') }}</textarea>
            </div>
        </div>
        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
            <x-action-button type="save">Create UOM</x-action-button>
        </div>
    </form>
</div>

<script>
function formatCode(input) {
    let value = input.value;
    // Replace spaces with underscores and convert to uppercase
    value = value.replace(/\s+/g, '_').toUpperCase();
    input.value = value;
}
</script>
@endsection


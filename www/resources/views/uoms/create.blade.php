@extends('layouts.app')

@section('title', 'Add UOM')

@section('content')
<div class="bg-white shadow rounded-lg">
    <form method="POST" action="{{ route('uoms.store') }}" class="space-y-6 p-6">
        @csrf
        
        <!-- Hidden fields with default values -->
        <input type="hidden" name="dimension_code" value="count">
        <input type="hidden" name="factor_to_base" value="1">
        <input type="hidden" name="offset_to_base" value="0">
        <input type="hidden" name="min_increment" value="1">
        
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-gray-700">Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="e.g., Box of 10" required>
                <p class="mt-1 text-xs text-gray-500">Display name for this unit (e.g., "Box of 12", "Carton")</p>
                @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Code <span class="text-red-500">*</span></label>
                <input type="text" name="code" value="{{ old('code') }}" id="code" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="e.g., BOX10" required oninput="formatCode(this)">
                <p class="mt-1 text-xs text-gray-500">Short code (e.g., "BOX10", "CTN20X12")</p>
                @error('code')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Units per UOM <span class="text-red-500">*</span></label>
                <input type="number" name="units_per_uom" value="{{ old('units_per_uom', 1) }}" min="1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="e.g., 10" required>
                <p class="mt-1 text-xs text-gray-500">How many pieces are in this UOM? (e.g., 10 for a box of 10)</p>
                @error('units_per_uom')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('status', 1) == 0 ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="e.g., Box containing 10 cans">{{ old('description') }}</textarea>
                <p class="mt-1 text-xs text-gray-500">Additional details about this UOM (optional)</p>
                @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>
        
        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
            <a href="{{ route('uoms.index') }}" class="btn btn-secondary">
                <i class="btn-icon fa-solid fa-times"></i>
                Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="btn-icon fa-solid fa-check"></i>
                Create UOM
            </button>
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

// Auto-sync units_per_uom with factor_to_base
document.querySelector('input[name="units_per_uom"]').addEventListener('input', function() {
    document.querySelector('input[name="factor_to_base"]').value = this.value;
});
</script>
@endsection


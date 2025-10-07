@extends('layouts.app')

@section('title', 'Create New Role')
@section('description', 'Create a new role with specific permissions')

@section('actions')
<a href="{{ route('user-management.roles') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
    <i class="fas fa-arrow-left mr-2"></i>
    Back to Roles
</a>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-6">
                <i class="fas fa-user-tag mr-2"></i>
                Create New Role
            </h3>

            <form method="POST" action="{{ route('user-management.roles.store') }}">
                @csrf
                
                <div class="space-y-8">
                    <!-- Role Information Section -->
                    <div>
                        <h4 class="text-md font-medium text-gray-900 mb-4">Role Information</h4>
                        <div class="space-y-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Role Name *</label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('name') border-red-300 @enderror"
                                       placeholder="e.g., sales_manager">
                                <p class="mt-1 text-xs text-gray-500">Unique identifier for the role (lowercase, underscores)</p>
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="display_name" class="block text-sm font-medium text-gray-700">Display Name *</label>
                                <input type="text" name="display_name" id="display_name" value="{{ old('display_name') }}" required
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('display_name') border-red-300 @enderror"
                                       placeholder="e.g., Sales Manager">
                                <p class="mt-1 text-xs text-gray-500">Human-readable name for the role</p>
                                @error('display_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea name="description" id="description" rows="3"
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('description') border-red-300 @enderror"
                                          placeholder="Describe the role's responsibilities and scope">{{ old('description') }}</textarea>
                                @error('description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Permissions Section -->
                    <div>
                        <h4 class="text-md font-medium text-gray-900 mb-4">Assign Permissions</h4>
                        
                        @if($permissions->count() > 0)
                            <div class="space-y-4 max-h-96 overflow-y-auto border border-gray-200 rounded-lg p-4">
                                @foreach($permissions as $module => $modulePermissions)
                                    <div class="border-b border-gray-100 pb-4 last:border-b-0">
                                        <h5 class="text-sm font-medium text-gray-900 mb-3">{{ ucfirst(str_replace('_', ' ', $module)) }}</h5>
                                        <div class="space-y-3">
                                            @foreach($modulePermissions as $permission)
                                                <label class="flex items-start p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                                    <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" 
                                                           {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}
                                                           class="mt-1 rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                                    <div class="ml-3 flex-1 min-w-0">
                                                        <span class="text-sm font-medium text-gray-700">{{ $permission->display_name }}</span>
                                                        @if($permission->description)
                                                            <p class="text-xs text-gray-500 mt-1">{{ $permission->description }}</p>
                                                        @endif
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="mt-4 flex flex-col sm:flex-row justify-between items-center space-y-2 sm:space-y-0">
                                <button type="button" onclick="selectAllPermissions()" class="w-full sm:w-auto px-4 py-2 text-sm text-blue-600 hover:text-blue-800 border border-blue-300 rounded-md hover:bg-blue-50">
                                    Select All
                                </button>
                                <button type="button" onclick="deselectAllPermissions()" class="w-full sm:w-auto px-4 py-2 text-sm text-gray-600 hover:text-gray-800 border border-gray-300 rounded-md hover:bg-gray-50">
                                    Deselect All
                                </button>
                            </div>
                        @else
                            <div class="text-center py-8 border border-gray-200 rounded-lg">
                                <i class="fas fa-key text-4xl text-gray-400 mb-4"></i>
                                <p class="text-sm text-gray-500">No permissions available</p>
                                <a href="{{ route('user-management.permissions') }}" class="text-sm text-blue-600 hover:text-blue-800">
                                    Manage Permissions
                                </a>
                            </div>
                        @endif
                        
                        @error('permissions')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="mt-8 flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3">
                    <a href="{{ route('user-management.roles') }}" 
                       class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-3 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Cancel
                    </a>
                    <button type="submit" 
                            class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-3 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-save mr-2"></i>
                        Create Role
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function selectAllPermissions() {
    document.querySelectorAll('input[name="permissions[]"]').forEach(checkbox => {
        checkbox.checked = true;
    });
}

function deselectAllPermissions() {
    document.querySelectorAll('input[name="permissions[]"]').forEach(checkbox => {
        checkbox.checked = false;
    });
}
</script>
@endsection

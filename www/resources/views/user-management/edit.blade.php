@extends('layouts.app')

@section('title', 'Edit User')
@section('description', 'Edit user details and role assignments')

@section('actions')
<a href="{{ route('user-management.show', ['user_management' => $user_management->id]) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
    <i class="fas fa-eye mr-2"></i>
    View User
</a>
<a href="{{ route('user-management.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
    <i class="fas fa-arrow-left mr-2"></i>
    Back to Users
</a>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-6">
                <i class="fas fa-user-edit mr-2"></i>
                Edit User: {{ $user_management->name }}
            </h3>

            <form method="POST" action="{{ route('user-management.update', ['user_management' => $user_management->id]) }}">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Full Name -->
                    <div class="sm:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700">Full Name *</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user_management->name) }}" required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('name') border-red-300 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Username -->
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700">Username *</label>
                        <input type="text" name="username" id="username" value="{{ old('username', $user_management->username) }}" required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('username') border-red-300 @enderror">
                        @error('username')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user_management->email) }}" required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('email') border-red-300 @enderror">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                        <input type="password" name="password" id="password"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('password') border-red-300 @enderror"
                               placeholder="Leave blank to keep current password">
                        <p class="mt-1 text-xs text-gray-500">Leave blank to keep current password</p>
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               placeholder="Confirm new password">
                    </div>

                    <!-- User Level -->
                    <div>
                        <label for="user_level" class="block text-sm font-medium text-gray-700">User Level *</label>
                        <select name="user_level" id="user_level" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('user_level') border-red-300 @enderror">
                            <option value="">Select Level</option>
                            <option value="Normal" {{ old('user_level', $user_management->user_level) === 'Normal' ? 'selected' : '' }}>Normal</option>
                            <option value="Admin" {{ old('user_level', $user_management->user_level) === 'Admin' ? 'selected' : '' }}>Admin</option>
                            <option value="Root" {{ old('user_level', $user_management->user_level) === 'Root' ? 'selected' : '' }}>Root</option>
                        </select>
                        @error('user_level')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Job Title -->
                    <div>
                        <label for="job_title" class="block text-sm font-medium text-gray-700">Job Title</label>
                        <input type="text" name="job_title" id="job_title" value="{{ old('job_title', $user_management->job_title) }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('job_title') border-red-300 @enderror">
                        @error('job_title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Department -->
                    <div>
                        <label for="department_id" class="block text-sm font-medium text-gray-700">Department/Location</label>
                        <select name="department_id" id="department_id"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('department_id') border-red-300 @enderror">
                            <option value="">Unassigned</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ (string) old('department_id', $user_management->department_id) === (string) $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('department_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status *</label>
                        <select name="status" id="status" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('status') border-red-300 @enderror">
                            <option value="1" {{ old('status', $user_management->status) == 1 ? 'selected' : '' }}>Active</option>
                            <option value="2" {{ old('status', $user_management->status) == 2 ? 'selected' : '' }}>Inactive</option>
                            <option value="0" {{ old('status', $user_management->status) == 0 ? 'selected' : '' }}>Deleted</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Roles -->
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Assign Roles</label>
                        <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach($roles as $role)
                                @php $roleName = strtolower($role->name); $roleDisplay = strtolower($role->display_name); @endphp
                                <label class="flex items-center">
                                    <input type="checkbox" name="roles[]" value="{{ $role->id }}" 
                                           {{ in_array($role->id, old('roles', $userRoles)) ? 'checked' : '' }}
                                           data-role-name="{{ $roleName }}" data-role-display="{{ $roleDisplay }}"
                                           class="role-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">{{ $role->display_name }}</span>
                                    @if($role->is_system)
                                        <span class="ml-1 text-xs text-purple-600">(System)</span>
                                    @endif
                                </label>
                            @endforeach
                        </div>
                        @error('roles')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- User Information -->
                <div class="mt-6 bg-gray-50 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-900 mb-3">User Information</h4>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <span class="text-sm text-gray-500">Created:</span>
                            <span class="text-sm font-medium text-gray-900">{{ $user_management->created_at->format('M d, Y H:i') }}</span>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">Last Updated:</span>
                            <span class="text-sm font-medium text-gray-900">{{ $user_management->updated_at->format('M d, Y H:i') }}</span>
                        </div>
                    <div>
                            <span class="text-sm text-gray-500">Last Login:</span>
                            <span class="text-sm font-medium text-gray-900">{{ $user_management->last_login ? \Carbon\Carbon::parse($user_management->last_login)->format('M d, Y H:i') : 'Never' }}</span>
                        </div>
                    <div>
                        <span class="text-sm text-gray-500">Department:</span>
                        <span class="text-sm font-medium text-gray-900">{{ optional($user_management->department)->name ?? 'Unassigned' }}</span>
                    </div>
                        <div>
                            <span class="text-sm text-gray-500">Current Roles:</span>
                            <span class="text-sm font-medium text-gray-900">{{ $user_management->roles->count() }} roles</span>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="mt-6 flex justify-end space-x-3">
                    <x-action-button type="cancel" :href="route('user-management.show', ['user_management' => $user_management->id])" />
                    <x-action-button type="save">Update User</x-action-button>
                </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
<script>
(function() {
  function toggleAdminRole() {
    var level = document.getElementById('user_level').value;
    var checkboxes = document.querySelectorAll('.role-checkbox');
    checkboxes.forEach(function(cb) {
      var rn = (cb.dataset.roleName || '').toLowerCase();
      var rd = (cb.dataset.roleDisplay || '').toLowerCase();
      var isAdminRole = rn === 'admin' || rn === 'administrator' || rd.indexOf('admin') !== -1;
      if (isAdminRole) {
        if (level === 'Normal') {
          cb.checked = false;
          cb.disabled = true;
          var label = cb.closest('label');
          if (label) { label.classList.add('opacity-60'); label.classList.add('hidden'); }
        } else {
          cb.disabled = false;
          var label = cb.closest('label');
          if (label) { label.classList.remove('opacity-60'); label.classList.remove('hidden'); }
        }
      }
    });
  }
  document.getElementById('user_level').addEventListener('change', toggleAdminRole);
  document.addEventListener('DOMContentLoaded', toggleAdminRole);
  toggleAdminRole();
})();
</script>
@endpush
@endsection

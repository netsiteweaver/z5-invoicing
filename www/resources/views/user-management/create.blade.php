@extends('layouts.app')

@section('title', 'Add New User')
@section('description', 'Create a new user account')

@section('actions')
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
                <i class="fas fa-user-plus mr-2"></i>
                Add New User
            </h3>

            @if(session('success'))
            <div class="mb-4 rounded-md bg-green-50 p-4 text-sm text-green-800">{{ session('success') }}</div>
            @endif
            @if($errors->has('error'))
            <div class="mb-4 rounded-md bg-red-50 p-4 text-sm text-red-700">{{ $errors->first('error') }}</div>
            @endif
            @if($errors->any())
            <div class="mb-4 rounded-md bg-red-50 p-4 text-sm text-red-700">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('user-management.store') }}">
                @csrf
                
                <div class="space-y-6">
                    <!-- Basic Information Section -->
                    <div>
                        <h4 class="text-md font-medium text-gray-900 mb-4">Basic Information</h4>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <!-- Full Name -->
                            <div class="sm:col-span-2">
                                <label for="name" class="block text-sm font-medium text-gray-700">Full Name *</label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('name') border-red-300 @enderror">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Username -->
                            <div>
                                <label for="username" class="block text-sm font-medium text-gray-700">Username *</label>
                                <input type="text" name="username" id="username" value="{{ old('username') }}" required
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('username') border-red-300 @enderror">
                                @error('username')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('email') border-red-300 @enderror">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Security Section -->
                    <div>
                        <h4 class="text-md font-medium text-gray-900 mb-4">Security</h4>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <!-- Password -->
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700">Password *</label>
                                <input type="password" name="password" id="password" required
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('password') border-red-300 @enderror">
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password *</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" required
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>
                        </div>
                    </div>

                    <!-- User Details Section -->
                    <div>
                        <h4 class="text-md font-medium text-gray-900 mb-4">User Details</h4>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <!-- User Level -->
                            <div>
                                <label for="user_level" class="block text-sm font-medium text-gray-700">User Level *</label>
                                <select name="user_level" id="user_level" required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('user_level') border-red-300 @enderror">
                                    <option value="">Select Level</option>
                                    <option value="Normal" {{ old('user_level') === 'Normal' ? 'selected' : '' }}>Normal</option>
                                    <option value="Admin" {{ old('user_level') === 'Admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="Root" {{ old('user_level') === 'Root' ? 'selected' : '' }}>Root</option>
                                </select>
                                @error('user_level')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Job Title -->
                            <div>
                                <label for="job_title" class="block text-sm font-medium text-gray-700">Job Title</label>
                                <input type="text" name="job_title" id="job_title" value="{{ old('job_title') }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('job_title') border-red-300 @enderror">
                                @error('job_title')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Department -->
                            <div class="sm:col-span-2">
                                <label for="department_id" class="block text-sm font-medium text-gray-700">Department/Location</label>
                                <select name="department_id" id="department_id"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('department_id') border-red-300 @enderror">
                                    <option value="">Unassigned</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                            {{ $dept->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Roles Section -->
                    <div>
                        <h4 class="text-md font-medium text-gray-900 mb-4">Assign Roles</h4>
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach($roles as $role)
                                @php $roleName = strtolower($role->name); $roleDisplay = strtolower($role->display_name); @endphp
                                <label class="flex items-start p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" name="roles[]" value="{{ $role->id }}" 
                                           {{ in_array($role->id, old('roles', [])) ? 'checked' : '' }}
                                           data-role-name="{{ $roleName }}" data-role-display="{{ $roleDisplay }}"
                                           class="role-checkbox mt-1 rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <div class="ml-3">
                                        <span class="text-sm font-medium text-gray-700">{{ $role->display_name }}</span>
                                        @if($role->description)
                                            <p class="text-xs text-gray-500 mt-1">{{ $role->description }}</p>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('roles')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="mt-8 flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3">
                    <a href="{{ route('user-management.index') }}" 
                       class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-3 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Cancel
                    </a>
                    <button type="submit" 
                            class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-3 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-save mr-2"></i>
                        Create User
                    </button>
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

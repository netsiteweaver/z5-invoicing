@extends('layouts.app')

@section('title', 'User Details')
@section('description', 'View and manage user details, roles, and permissions')

@section('actions')
<a href="{{ route('user-management.edit', ['user_management' => $user_management->id]) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
    <i class="fas fa-edit mr-2"></i>
    Edit User
</a>
<a href="{{ route('user-management.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
    <i class="fas fa-arrow-left mr-2"></i>
    Back to Users
</a>
@endsection

@section('content')
<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <!-- User Details -->
    <div class="lg:col-span-2">
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    <i class="fas fa-user mr-2"></i>
                    User Information
                </h3>
                
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Full Name</label>
                        <div class="mt-1 text-sm text-gray-900">{{ $user_management->name }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Username</label>
                        <div class="mt-1 text-sm text-gray-900">{{ $user_management->username }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Email</label>
                        <div class="mt-1 text-sm text-gray-900">{{ $user_management->email }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">User Level</label>
                        <div class="mt-1">
                            @if($user_management->user_level === 'Admin')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">{{ $user_management->user_level }}</span>
                            @elseif($user_management->user_level === 'Root')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">{{ $user_management->user_level }}</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{{ $user_management->user_level }}</span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Job Title</label>
                        <div class="mt-1 text-sm text-gray-900">{{ $user_management->job_title ?? 'Not specified' }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Department/Location</label>
                        <div class="mt-1 text-sm text-gray-900">{{ optional($user_management->department)->name ?? 'Unassigned' }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Status</label>
                        <div class="mt-1">
                            @if($user_management->status === 1)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                            @elseif($user_management->status === 2)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Inactive</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Deleted</span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Created</label>
                        <div class="mt-1 text-sm text-gray-900">{{ $user_management->created_at->format('M d, Y H:i') }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Last Login</label>
                        <div class="mt-1 text-sm text-gray-900">{{ $user_management->last_login ? \Carbon\Carbon::parse($user_management->last_login)->format('M d, Y H:i') : 'Never' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Roles -->
        <div class="mt-6 bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        <i class="fas fa-user-tag mr-2"></i>
                        Assigned Roles
                    </h3>
                    <button onclick="openRoleModal()" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-plus mr-1"></i>
                        Assign Role
                    </button>
                </div>
                
                @if($user_management->roles->count() > 0)
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($user_management->roles as $role)
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-900">{{ $role->display_name }}</h4>
                                        <p class="text-sm text-gray-500">{{ $role->description }}</p>
                                        <div class="mt-2">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $role->is_system ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ $role->is_system ? 'System Role' : 'Custom Role' }}
                                            </span>
                                        </div>
                                    </div>
                                    @if(!$role->is_system || auth()->user()->hasRole('admin'))
                                        <form method="POST" action="{{ route('user-management.remove-role', ['user_management' => $user_management->id]) }}" class="inline" onsubmit="return confirm('Remove this role from user?')">
                                            @csrf
                                            <input type="hidden" name="role_id" value="{{ $role->id }}">
                                            <button type="submit" class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-user-tag text-4xl text-gray-400 mb-4"></i>
                        <p class="text-sm text-gray-500">No roles assigned to this user.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- User Permissions -->
        <div class="mt-6 bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    <i class="fas fa-key mr-2"></i>
                    Effective Permissions
                </h3>
                
                @php
                    $userPermissions = $user_management->getAllPermissions()->groupBy('module');
                @endphp
                
                @if($userPermissions->count() > 0)
                    <div class="space-y-4">
                        @foreach($userPermissions as $module => $permissions)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <h4 class="text-sm font-medium text-gray-900 mb-2">{{ ucfirst(str_replace('_', ' ', $module)) }}</h4>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($permissions as $permission)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            {{ $permission->display_name }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-key text-4xl text-gray-400 mb-4"></i>
                        <p class="text-sm text-gray-500">No permissions assigned to this user.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="lg:col-span-1">
        <!-- Quick Actions -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    <i class="fas fa-bolt mr-2"></i>
                    Quick Actions
                </h3>
                
                <div class="space-y-3">
                    <a href="{{ route('user-management.edit', ['user_management' => $user_management->id]) }}" class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <i class="fas fa-edit mr-2"></i>
                        Edit User
                    </a>
                    
                    <button onclick="openRoleModal()" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <i class="fas fa-user-tag mr-2"></i>
                        Assign Role
                    </button>
                    
                    @if($user_management->id !== auth()->id())
                        <form method="POST" action="{{ route('user-management.destroy', ['user_management' => $user_management->id]) }}" onsubmit="return confirm('Are you sure you want to delete this user?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-red-300 shadow-sm text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50">
                                <i class="fas fa-trash mr-2"></i>
                                Delete User
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Role Statistics -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    <i class="fas fa-chart-pie mr-2"></i>
                    Role Statistics
                </h3>
                
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Total Roles:</span>
                        <span class="text-sm font-medium">{{ $user_management->roles->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Total Permissions:</span>
                        <span class="text-sm font-medium">{{ $user_management->getAllPermissions()->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">System Roles:</span>
                        <span class="text-sm font-medium">{{ $user_management->roles->where('is_system', true)->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Custom Roles:</span>
                        <span class="text-sm font-medium">{{ $user_management->roles->where('is_system', false)->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Role Assignment Modal -->
<div id="roleModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" onclick="if(event.target === this) closeRoleModal()">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Assign Role</h3>
                <button onclick="closeRoleModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="roleForm" method="POST">
                @csrf
                <input type="hidden" name="_method" value="POST">
                
                <div class="mb-4">
                    <label for="role_id" class="block text-sm font-medium text-gray-700 mb-2">Select Role</label>
                    @php
                        $unassignedRoles = $allRoles->filter(function($r) use ($user_management) {
                            return !$user_management->hasRole($r);
                        });
                    @endphp
                    @if($unassignedRoles->isEmpty())
                        <div class="px-3 py-2 bg-yellow-50 border border-yellow-200 rounded text-sm text-yellow-800">
                            All available roles are already assigned to this user.
                        </div>
                    @else
                        <select name="role_id" id="role_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                            <option value="">Choose a role</option>
                            @foreach($unassignedRoles as $role)
                                <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeRoleModal()" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700" {{ isset($unassignedRoles) && $unassignedRoles->isEmpty() ? 'disabled' : '' }}>
                        Assign Role
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openRoleModal() {
    document.getElementById('roleForm').action = '{{ route("user-management.assign-role", ["user_management" => $user_management->id]) }}';
    document.getElementById('roleModal').classList.remove('hidden');
}

function closeRoleModal() {
    document.getElementById('roleModal').classList.add('hidden');
}
</script>
@endsection

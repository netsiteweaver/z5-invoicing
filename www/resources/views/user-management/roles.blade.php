@extends('layouts.app')

@section('title', 'Manage Roles')
@section('description', 'Create and manage user roles and permissions')

@section('actions')
<a href="{{ route('user-management.roles.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
    <i class="fas fa-plus mr-2"></i>
    Create Role
</a>
<a href="{{ route('user-management.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
    <i class="fas fa-users mr-2"></i>
    Back to Users
</a>
@endsection

@section('content')
<!-- Roles Table -->
@if($roles->count() > 0)
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <!-- Mobile cards (visible on small screens) -->
            <div class="sm:hidden space-y-4">
                @foreach($roles as $role)
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <!-- Role Header -->
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1 min-w-0">
                                <div class="text-base font-medium text-gray-900 truncate">{{ $role->display_name }}</div>
                                <div class="text-sm text-gray-500 truncate">{{ $role->name }}</div>
                            </div>
                            <div class="flex items-center space-x-2 ml-3">
                                @if($role->is_system)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">System</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Custom</span>
                                @endif
                                @if($role->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Inactive</span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Description -->
                        <div class="mb-3">
                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Description</span>
                            <div class="mt-1 text-sm text-gray-900">{{ $role->description ?? 'No description' }}</div>
                        </div>
                        
                        <!-- Stats -->
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Permissions</span>
                                <div class="mt-1 text-sm text-gray-900">{{ $role->permissions->count() }} permissions</div>
                                @if($role->permissions->count() > 0)
                                    <div class="text-xs text-gray-500 mt-1 line-clamp-2">
                                        {{ $role->permissions->take(2)->pluck('display_name')->join(', ') }}
                                        @if($role->permissions->count() > 2)
                                            +{{ $role->permissions->count() - 2 }} more
                                        @endif
                                    </div>
                                @endif
                            </div>
                            <div>
                                <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Users</span>
                                <div class="mt-1 text-sm text-gray-900">{{ $role->users->count() }} users</div>
                            </div>
                        </div>
                        
                        <!-- Actions -->
                        <div class="flex flex-wrap gap-2">
                            @if(!$role->is_system)
                                <a href="{{ route('user-management.roles.edit', $role) }}" class="btn btn-edit">
                                    <i class="btn-icon fas fa-edit"></i>
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('user-management.roles.toggle', $role) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-warning">
                                        <i class="btn-icon fas fa-{{ $role->is_active ? 'pause' : 'play' }}"></i>
                                        {{ $role->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('user-management.roles.destroy', $role) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this role?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-delete">
                                        <i class="btn-icon fas fa-trash"></i>
                                        Delete
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('user-management.roles.show', $role) }}" class="btn btn-view">
                                    <i class="btn-icon fas fa-eye"></i>
                                    View
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Desktop table (hidden on small screens) -->
            <div class="hidden sm:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Permissions</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Users</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($roles as $role)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $role->display_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $role->name }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $role->description ?? 'No description' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($role->is_system)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">System Role</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Custom Role</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $role->permissions->count() }} permissions</div>
                                    @if($role->permissions->count() > 0)
                                        <div class="text-xs text-gray-500">
                                            {{ $role->permissions->take(3)->pluck('display_name')->join(', ') }}
                                            @if($role->permissions->count() > 3)
                                                +{{ $role->permissions->count() - 3 }} more
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $role->users->count() }} users</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($role->is_active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        @if(!$role->is_system)
                                            <a href="{{ route('user-management.roles.edit', $role) }}" class="text-blue-600 hover:text-blue-900" title="Edit Role">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" action="{{ route('user-management.roles.toggle', $role) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-yellow-600 hover:text-yellow-900" title="{{ $role->is_active ? 'Deactivate' : 'Activate' }}">
                                                    <i class="fas fa-{{ $role->is_active ? 'pause' : 'play' }}"></i>
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('user-management.roles.destroy', $role) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this role?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" title="Delete Role">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @else
                                            <a href="{{ route('user-management.roles.show', $role) }}" class="text-gray-600 hover:text-gray-900" title="View Role">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Pagination -->
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $roles->links() }}
        </div>
    </div>
@else
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="text-center py-12">
                <i class="fas fa-user-tag text-6xl text-gray-400 mb-4"></i>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No roles found</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating a new role.</p>
                <div class="mt-6">
                    <a href="{{ route('user-management.roles.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-plus mr-2"></i>
                        Create Role
                    </a>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection

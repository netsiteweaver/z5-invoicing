@extends('layouts.app')

@section('title', 'User Management')
@section('description', 'Manage users, roles, and permissions')

@section('actions')
<x-action-button type="create" :href="route('user-management.create')" icon="fa-solid fa-user-plus">
    Add User
</x-action-button>
<x-action-button type="settings" :href="route('user-management.roles')" icon="fa-solid fa-user-tag">
    Manage Roles
</x-action-button>
<x-action-button type="settings" :href="route('user-management.permissions')" icon="fa-solid fa-key">
    Manage Permissions
</x-action-button>
@endsection

@section('content')
<!-- Filters -->
<div class="bg-white shadow rounded-lg mb-6" x-data="{ mobileOpen: false }">
    <div class="px-4 py-5 sm:p-6">
        <!-- Mobile toggle -->
        <div class="flex items-center justify-between sm:hidden mb-4">
            <span class="text-sm font-medium text-gray-700">Filters</span>
            <button type="button" @click="mobileOpen = !mobileOpen" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                <span x-show="!mobileOpen">Show Filters</span>
                <span x-show="mobileOpen">Hide Filters</span>
                <i class="fas fa-chevron-down ml-2 transform transition-transform" :class="mobileOpen ? 'rotate-180' : ''"></i>
            </button>
        </div>
        
        <!-- Form -->
        <div class="mt-4 sm:mt-0" x-cloak x-show="mobileOpen || window.matchMedia('(min-width: 640px)').matches">
            <form method="GET" class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                           placeholder="Name, email, username...">
                </div>
                
                <div>
                    <label for="role_id" class="block text-sm font-medium text-gray-700">Role</label>
                    <select name="role_id" id="role_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">All Roles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>
                                {{ $role->display_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-search mr-2"></i>
                        Filter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Users Table -->
@if($users->count() > 0)
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <!-- Mobile cards (visible on small screens) -->
            <div class="sm:hidden space-y-4">
                @foreach($users as $user)
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <!-- User Info -->
                        <div class="flex items-center mb-3">
                            <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                <span class="text-gray-600 font-medium text-lg">{{ substr($user->name, 0, 1) }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-base font-medium text-gray-900 truncate">{{ $user->name }}</div>
                                <div class="text-sm text-gray-500 truncate">{{ $user->email }}</div>
                                <div class="text-sm text-gray-600 truncate">@{{ $user->username }}</div>
                            </div>
                        </div>
                        
                        <!-- User Level -->
                        <div class="mb-3">
                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Level</span>
                            <div class="mt-1">
                                @if($user->user_level === 'Admin')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">{{ $user->user_level }}</span>
                                @elseif($user->user_level === 'Root')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">{{ $user->user_level }}</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{{ $user->user_level }}</span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Department -->
                        <div class="mb-3">
                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Department</span>
                            <div class="mt-1 text-sm text-gray-900">{{ $user->department?->name ?? '—' }}</div>
                        </div>
                        
                        <!-- Roles -->
                        <div class="mb-4">
                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Roles</span>
                            <div class="mt-1">
                                @if($user->roles->count() > 0)
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($user->roles as $role)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ $role->display_name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-sm text-gray-500">No roles assigned</span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Actions -->
                        <div class="flex flex-col space-y-2">
                            <div class="flex space-x-2">
                                <a href="{{ route('user-management.show', ['user_management' => $user->id]) }}" class="flex-1 btn btn-view text-center">
                                    <i class="btn-icon fa-regular fa-eye"></i>
                                    View
                                </a>
                                <a href="{{ route('user-management.edit', ['user_management' => $user->id]) }}" class="flex-1 btn btn-edit text-center">
                                    <i class="btn-icon fa-solid fa-pen"></i>
                                    Edit
                                </a>
                            </div>
                            @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('user-management.destroy', ['user_management' => $user->id]) }}" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full btn btn-delete">
                                        <i class="btn-icon fa-solid fa-trash"></i>
                                        Delete User
                                    </button>
                                </form>
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Level</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Roles</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($users as $user)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mr-3">
                                            <span class="text-gray-600 font-medium text-sm">{{ substr($user->name, 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $user->username }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $user->department?->name ?? '—' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($user->user_level === 'Admin')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">{{ $user->user_level }}</span>
                                    @elseif($user->user_level === 'Root')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">{{ $user->user_level }}</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{{ $user->user_level }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($user->roles as $role)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ $role->display_name }}
                                            </span>
                                        @endforeach
                                        @if($user->roles->isEmpty())
                                            <span class="text-sm text-gray-500">No roles assigned</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('user-management.show', ['user_management' => $user->id]) }}" class="btn btn-view">
                                            <i class="btn-icon fa-regular fa-eye"></i>
                                            View
                                        </a>
                                        <a href="{{ route('user-management.edit', ['user_management' => $user->id]) }}" class="btn btn-edit">
                                            <i class="btn-icon fa-solid fa-pen"></i>
                                            Edit
                                        </a>
                                        @if($user->id !== auth()->id())
                                            <form method="POST" action="{{ route('user-management.destroy', ['user_management' => $user->id]) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-delete">
                                                    <i class="btn-icon fa-solid fa-trash"></i>
                                                    Delete
                                                </button>
                                            </form>
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
            {{ $users->links() }}
        </div>
    </div>
@else
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="text-center py-12">
                <i class="fas fa-users text-6xl text-gray-400 mb-4"></i>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No users found</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by adding a new user.</p>
                <div class="mt-6">
                    <a href="{{ route('user-management.create') }}" class="btn btn-create">
                        <i class="btn-icon fa-solid fa-user-plus"></i>
                        Add User
                    </a>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection

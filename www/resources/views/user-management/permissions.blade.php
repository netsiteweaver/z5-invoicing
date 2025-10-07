@extends('layouts.app')

@section('title', 'Manage Permissions')
@section('description', 'View and manage system permissions')

@section('actions')
<a href="{{ route('user-management.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
    <i class="fas fa-users mr-2"></i>
    Back to Users
</a>
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
            <form method="GET" class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                <div>
                    <label for="module" class="block text-sm font-medium text-gray-700">Module</label>
                    <select name="module" id="module" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">All Modules</option>
                        @foreach($modules as $module)
                            <option value="{{ $module }}" {{ request('module') === $module ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $module)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="action" class="block text-sm font-medium text-gray-700">Action</label>
                    <select name="action" id="action" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">All Actions</option>
                        @foreach($actions as $action)
                            <option value="{{ $action }}" {{ request('action') === $action ? 'selected' : '' }}>
                                {{ ucfirst($action) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
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

<!-- Permissions Table -->
@if($permissions->count() > 0)
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <!-- Mobile cards (visible on small screens) -->
            <div class="sm:hidden space-y-4">
                @foreach($permissions as $permission)
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <!-- Permission Header -->
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1 min-w-0">
                                <div class="text-base font-medium text-gray-900 truncate">{{ $permission->display_name }}</div>
                                <div class="text-sm text-gray-500 truncate">{{ $permission->name }}</div>
                            </div>
                            <div class="flex items-center space-x-2 ml-3">
                                @if($permission->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Inactive</span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Module and Action -->
                        <div class="flex flex-wrap gap-2 mb-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ ucfirst(str_replace('_', ' ', $permission->module)) }}
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ ucfirst($permission->action) }}
                            </span>
                        </div>
                        
                        <!-- Description -->
                        <div class="mb-3">
                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Description</span>
                            <div class="mt-1 text-sm text-gray-900">{{ $permission->description ?? 'No description' }}</div>
                        </div>
                        
                        <!-- Roles -->
                        <div class="mb-4">
                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Roles</span>
                            <div class="mt-1">
                                <div class="text-sm text-gray-900">{{ $permission->roles->count() }} roles</div>
                                @if($permission->roles->count() > 0)
                                    <div class="text-xs text-gray-500 mt-1 line-clamp-2">
                                        {{ $permission->roles->take(2)->pluck('display_name')->join(', ') }}
                                        @if($permission->roles->count() > 2)
                                            +{{ $permission->roles->count() - 2 }} more
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Actions -->
                        <div class="flex justify-end">
                            <form method="POST" action="{{ route('user-management.permissions.toggle', $permission) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-warning">
                                    <i class="btn-icon fas fa-{{ $permission->is_active ? 'pause' : 'play' }}"></i>
                                    {{ $permission->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Desktop table (hidden on small screens) -->
            <div class="hidden sm:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Permission</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Module</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Roles</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($permissions as $permission)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $permission->display_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $permission->name }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ ucfirst(str_replace('_', ' ', $permission->module)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{ ucfirst($permission->action) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $permission->description ?? 'No description' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $permission->roles->count() }} roles</div>
                                    @if($permission->roles->count() > 0)
                                        <div class="text-xs text-gray-500">
                                            {{ $permission->roles->take(2)->pluck('display_name')->join(', ') }}
                                            @if($permission->roles->count() > 2)
                                                +{{ $permission->roles->count() - 2 }} more
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($permission->is_active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <form method="POST" action="{{ route('user-management.permissions.toggle', $permission) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-yellow-600 hover:text-yellow-900" title="{{ $permission->is_active ? 'Deactivate' : 'Activate' }}">
                                                <i class="fas fa-{{ $permission->is_active ? 'pause' : 'play' }}"></i>
                                            </button>
                                        </form>
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
            {{ $permissions->links() }}
        </div>
    </div>
@else
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="text-center py-12">
                <i class="fas fa-key text-6xl text-gray-400 mb-4"></i>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No permissions found</h3>
                <p class="mt-1 text-sm text-gray-500">No permissions match your current filters.</p>
                <div class="mt-6">
                    <a href="{{ route('user-management.permissions') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-refresh mr-2"></i>
                        Clear Filters
                    </a>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Permission Statistics -->
<div class="mt-6 grid grid-cols-2 gap-4 sm:gap-5 sm:grid-cols-2 lg:grid-cols-4">
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-4 sm:p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-key text-xl sm:text-2xl text-blue-500"></i>
                </div>
                <div class="ml-3 sm:ml-5 w-0 flex-1 min-w-0">
                    <dl>
                        <dt class="text-xs sm:text-sm font-medium text-gray-500 truncate">Total Permissions</dt>
                        <dd class="text-base sm:text-lg font-medium text-gray-900">{{ $permissions->total() }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-4 sm:p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-xl sm:text-2xl text-green-500"></i>
                </div>
                <div class="ml-3 sm:ml-5 w-0 flex-1 min-w-0">
                    <dl>
                        <dt class="text-xs sm:text-sm font-medium text-gray-500 truncate">Active Permissions</dt>
                        <dd class="text-base sm:text-lg font-medium text-gray-900">{{ $permissions->where('is_active', true)->count() }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-4 sm:p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-pause-circle text-xl sm:text-2xl text-red-500"></i>
                </div>
                <div class="ml-3 sm:ml-5 w-0 flex-1 min-w-0">
                    <dl>
                        <dt class="text-xs sm:text-sm font-medium text-gray-500 truncate">Inactive Permissions</dt>
                        <dd class="text-base sm:text-lg font-medium text-gray-900">{{ $permissions->where('is_active', false)->count() }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-4 sm:p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-layer-group text-xl sm:text-2xl text-purple-500"></i>
                </div>
                <div class="ml-3 sm:ml-5 w-0 flex-1 min-w-0">
                    <dl>
                        <dt class="text-xs sm:text-sm font-medium text-gray-500 truncate">Modules</dt>
                        <dd class="text-base sm:text-lg font-medium text-gray-900">{{ $modules->count() }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

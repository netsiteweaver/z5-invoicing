@extends('layouts.app')

@section('title', 'Role Details')
@section('description', 'View role details and assigned permissions')

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
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        <i class="fas fa-user-tag mr-2"></i>
                        {{ $role->display_name }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">{{ $role->name }}</p>
                </div>
                <div>
                    @if($role->is_system)
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">System Role</span>
                    @else
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Custom Role</span>
                    @endif
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div>
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Role Information</h4>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm text-gray-500">Description</dt>
                            <dd class="text-sm text-gray-900">{{ $role->description ?: 'No description' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Status</dt>
                            <dd class="text-sm text-gray-900">
                                @if($role->is_active)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">Inactive</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Users</dt>
                            <dd class="text-sm text-gray-900">{{ $role->users->count() }} users</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Created</dt>
                            <dd class="text-sm text-gray-900">{{ $role->created_at->format('M d, Y') }}</dd>
                        </div>
                    </dl>
                </div>

                <div>
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Assigned Permissions</h4>
                    @if($role->permissions->count() > 0)
                        <div class="space-y-4 max-h-96 overflow-y-auto border border-gray-200 rounded-lg p-4">
                            @foreach($permissions as $module => $modulePermissions)
                                @php $hasAny = $modulePermissions->pluck('id')->intersect($role->permissions->pluck('id'))->isNotEmpty(); @endphp
                                @if($hasAny)
                                <div class="border-b border-gray-100 pb-4 last:border-b-0">
                                    <h5 class="text-sm font-medium text-gray-900 mb-2">{{ ucfirst(str_replace('_', ' ', $module)) }}</h5>
                                    <ul class="space-y-1">
                                        @foreach($modulePermissions as $permission)
                                            @if($role->permissions->contains('id', $permission->id))
                                                <li class="text-sm text-gray-700">
                                                    <i class="fas fa-check text-green-500 mr-2"></i>{{ $permission->display_name }}
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="text-sm text-gray-500">No permissions assigned.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



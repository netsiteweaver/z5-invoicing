@extends('layouts.app')

@section('title', 'Department Details')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <a href="{{ route('departments.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $department->name }}</h1>
                <p class="mt-1 text-sm text-gray-500">
                    @if($department->is_main)
                        Main Department
                    @else
                        Department
                    @endif
                </p>
            </div>
        </div>
        <div class="flex space-x-3">
            @can('departments.edit')
            <a href="{{ route('departments.edit', $department) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Department
            </a>
            @endcan
            @can('departments.delete')
            <form method="POST" action="{{ route('departments.destroy', $department) }}" onsubmit="return confirm('Are you sure you want to delete this department?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Delete
                </button>
            </form>
            @endcan
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Department Information</h3>

                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Name</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $department->name }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                @if((int)$department->status === 1)
                                    Active
                                @elseif((int)$department->status === 2)
                                    Inactive
                                @else
                                    Deleted
                                @endif
                            </dd>
                        </div>

                        @if($department->email)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $department->email }}</dd>
                        </div>
                        @endif

                        @if($department->phone_number)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Phone</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $department->phone_number }}</dd>
                        </div>
                        @endif

                        @if($department->manager)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Manager</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $department->manager->name }}</dd>
                        </div>
                        @endif

                        @if($department->is_main)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Main Department</dt>
                            <dd class="mt-1 text-sm text-gray-900">Yes</dd>
                        </div>
                        @endif

                        @if($department->address)
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Address</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $department->address }}</dd>
                        </div>
                        @endif

                        @if($department->description)
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Description</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $department->description }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Metadata</h3>

                    <dl class="space-y-4">
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500">Created</dt>
                            <dd class="text-sm text-gray-900">{{ $department->created_at->format('M d, Y') }}</dd>
                        </div>
                        @if($department->updated_at && $department->updated_at->ne($department->created_at))
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                            <dd class="text-sm text-gray-900">{{ $department->updated_at->format('M d, Y') }}</dd>
                        </div>
                        @endif
                        @if($department->createdBy)
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500">Created By</dt>
                            <dd class="text-sm text-gray-900">{{ $department->createdBy->name }}</dd>
                        </div>
                        @endif
                        @if($department->updatedBy)
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500">Updated By</dt>
                            <dd class="text-sm text-gray-900">{{ $department->updatedBy->name }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


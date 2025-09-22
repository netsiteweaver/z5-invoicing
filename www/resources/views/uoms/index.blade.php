@extends('layouts.app')

@section('title', 'Units of Measure')

@section('actions')
@if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('uoms.create'))
<a href="{{ route('uoms.create') }}" class="btn btn-create">
    <i class="btn-icon fa-solid fa-plus"></i>
    Add UOM
</a>
@endif
@endsection

@section('content')
<div class="bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <form method="GET" class="mb-4">
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search UOMs..." class="mt-1 block w-64 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <button class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm bg-white hover:bg-gray-50">Search</button>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="show_inactive" value="1" {{ request('show_inactive') == '1' ? 'checked' : '' }} id="show_inactive" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" onchange="this.form.submit()">
                    <label for="show_inactive" class="ml-2 text-sm text-gray-700">Show inactive UOMs</label>
                </div>
            </div>
        </form>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Units/UOM</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($uoms as $uom)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $uom->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $uom->code }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $uom->units_per_uom }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{!! $uom->status ? '<span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">Active</span>' : '<span class="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-800">Inactive</span>' !!}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="flex space-x-2">
                                    <a href="{{ route('uoms.show', $uom) }}" class="btn btn-view">
                                        <i class="btn-icon fa-regular fa-eye"></i>
                                        View
                                    </a>
                                    @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('uoms.edit'))
                                    <a href="{{ route('uoms.edit', $uom) }}" class="btn btn-edit">
                                        <i class="btn-icon fa-solid fa-pen"></i>
                                        Edit
                                    </a>
                                    @endif
                                    @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('uoms.delete'))
                                    <form method="POST" action="{{ route('uoms.destroy', $uom) }}" class="inline" onsubmit="return confirm('Are you sure you want to deactivate this UOM?')">
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
    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
        {{ $uoms->links() }}
    </div>
</div>
@endsection


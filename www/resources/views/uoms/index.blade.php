@extends('layouts.app')

@section('title', 'Units of Measure')

@section('actions')
@if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('uoms.create'))
<a href="{{ route('uoms.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">Add UOM</a>
@endif
@endsection

@section('content')
<div class="bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <form method="GET" class="mb-4">
            <div class="flex items-center space-x-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search UOMs..." class="mt-1 block w-64 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <button class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm bg-white hover:bg-gray-50">Search</button>
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
                                @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('uoms.edit'))
                                <a href="{{ route('uoms.edit', $uom) }}" class="text-blue-600 hover:text-blue-800">Edit</a>
                                @endif
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


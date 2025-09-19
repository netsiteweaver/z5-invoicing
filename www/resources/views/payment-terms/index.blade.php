@extends('layouts.app')

@section('title', 'Payment Terms')

@section('actions')
<a href="{{ route('payment-terms.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">Add Term</a>
@endsection

@section('content')
<div class="bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Days</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Default</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($terms as $term)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $term->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ strtoupper($term->type) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $term->type === 'days' ? $term->days : '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">@if($term->is_default)<span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">Default</span>@endif</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{!! $term->status ? '<span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">Active</span>' : '<span class="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-800">Inactive</span>' !!}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="{{ route('payment-terms.edit', $term) }}" class="text-blue-600 hover:text-blue-800">Edit</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
        {{ $terms->links() }}
    </div>
    </div>
@endsection




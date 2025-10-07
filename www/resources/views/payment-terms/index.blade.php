@extends('layouts.app')

@section('title', 'Payment Terms')

@section('actions')
<x-action-button type="create" :href="route('payment-terms.create')">
    Add Term
</x-action-button>
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
                                <div class="flex space-x-2">
                                    <x-action-button type="view" :href="route('payment-terms.show', $term)" />
                                    <x-action-button type="edit" :href="route('payment-terms.edit', $term)" />
                                    <x-action-button 
                                        type="delete" 
                                        :form-action="route('payment-terms.destroy', $term)"
                                        confirm-message="Are you sure you want to delete this payment term?"
                                    />
                                </div>
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




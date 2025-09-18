@extends('layouts.app')

@section('title', 'Suppliers')
@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Suppliers</h1>
            <p class="mt-1 text-sm text-gray-500">Manage your supplier database</p>
        </div>
        <a href="{{ route('suppliers.create') }}" class="btn btn-create">
            <i class="btn-icon fa-solid fa-plus"></i>
            Add Supplier
        </a>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <form method="GET" class="grid grid-cols-1 gap-4 sm:grid-cols-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" 
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                       placeholder="Name, email, phone, contact...">
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="">Active (default)</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="-ml-1 mr-2 fa-solid fa-magnifying-glass"></i>
                    Filter
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        @if($suppliers->count() > 0)
            <ul class="divide-y divide-gray-200">
                @foreach($suppliers as $supplier)
                    <li>
                        <div class="px-4 py-4 sm:px-6 hover:bg-gray-50">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="h-10 w-10 rounded-full bg-purple-500 flex items-center justify-center">
                                            <span class="text-sm font-medium text-white">
                                                {{ strtoupper(substr($supplier->name, 0, 1)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="flex items-center">
                                            <p class="text-sm font-medium text-blue-600 truncate">
                                                {{ $supplier->name }}
                                            </p>
                                            @if((int)($supplier->status) === 1)
                                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                                            @else
                                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Inactive</span>
                                            @endif
                                        </div>
                                        <div class="mt-1 flex items-center text-sm text-gray-500">
                                            @if($supplier->email)
                                                <svg class="flex-shrink-0 mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                </svg>
                                                {{ $supplier->email }}
                                            @endif
                                            @if($supplier->phone_number1)
                                                <svg class="flex-shrink-0 mr-1.5 h-4 w-4 ml-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                                </svg>
                                                {{ $supplier->phone_number1 }}
                                            @endif
                                        </div>
                                        @if($supplier->city)
                                            <div class="mt-1 text-sm text-gray-500">
                                                <svg class="inline-flex mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                {{ $supplier->city }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-view">
                                        <i class="btn-icon fa-regular fa-eye"></i>
                                        View
                                    </a>
                                    <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-edit">
                                        <i class="btn-icon fa-solid fa-pen"></i>
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this supplier?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-delete">
                                            <i class="btn-icon fa-solid fa-trash"></i>
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>

            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $suppliers->withQueryString()->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No suppliers</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating a new supplier.</p>
                <div class="mt-6">
                    <a href="{{ route('suppliers.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Add Supplier
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection


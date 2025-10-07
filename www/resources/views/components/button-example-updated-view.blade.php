{{--
    EXAMPLE: Updated Customer Index Page using Action Button Component
    
    This file demonstrates how to migrate from old button syntax to new component syntax.
    Copy the patterns below to update your views.
--}}

@extends('layouts.app')

@section('title', 'Customers')

@section('content')
<div class="space-y-6">
    <!-- Header with Create Button -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Customers</h1>
            <p class="mt-1 text-sm text-gray-500">Manage your customer database</p>
        </div>
        @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('customers.create'))
        {{-- NEW WAY: Simple, clean, consistent --}}
        <x-action-button type="create" :href="route('customers.create')">
            Add Customer
        </x-action-button>
        @endif
    </div>

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg">
        <div class="p-4 sm:p-6">
            <form method="GET" class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" 
                           placeholder="Name, email, phone...">
                </div>
                
                <div class="flex items-end">
                    {{-- NEW WAY: Search button --}}
                    <x-action-button type="search" />
                </div>
            </form>
        </div>
    </div>

    <!-- Customers List -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        @if($customers->count() > 0)
            <!-- Desktop List -->
            <ul class="hidden sm:block divide-y divide-gray-200">
                @foreach($customers as $customer)
                    <li>
                        <div class="px-4 py-4 sm:px-6 hover:bg-gray-50">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center">
                                            <span class="text-sm font-medium text-white">
                                                {{ strtoupper(substr($customer->display_name, 0, 1)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-blue-600">
                                            {{ $customer->display_name }}
                                        </p>
                                        @if($customer->email)
                                            <p class="text-sm text-gray-500">{{ $customer->email }}</p>
                                        @endif
                                    </div>
                                </div>
                                
                                {{-- NEW WAY: Action buttons - clean and consistent --}}
                                <div class="flex items-center space-x-2">
                                    <x-action-button type="view" :href="route('customers.show', $customer)" />
                                    
                                    @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('customers.edit'))
                                    <x-action-button type="edit" :href="route('customers.edit', $customer)" />
                                    @endif
                                    
                                    @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('customers.delete'))
                                    <x-action-button 
                                        type="delete" 
                                        :form-action="route('customers.destroy', $customer)"
                                        confirm-message="Are you sure you want to delete {{ $customer->display_name }}?"
                                    />
                                    @endif
                                </div>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
            
            <!-- Mobile Cards -->
            <div class="sm:hidden space-y-4 p-4">
                @foreach($customers as $customer)
                    <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                        <!-- Customer Header -->
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center min-w-0 flex-1">
                                <div class="w-12 h-12 rounded-full bg-blue-500 flex items-center justify-center mr-3">
                                    <span class="text-sm font-medium text-white">
                                        {{ strtoupper(substr($customer->display_name, 0, 1)) }}
                                    </span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h3 class="text-sm font-medium text-gray-900 truncate">
                                        {{ $customer->display_name }}
                                    </h3>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Contact Information -->
                        @if($customer->email)
                            <div class="mb-3 text-sm text-gray-600">
                                {{ $customer->email }}
                            </div>
                        @endif
                        
                        {{-- NEW WAY: Mobile actions with icon-only option --}}
                        <div class="flex space-x-2 pt-3 border-t border-gray-100">
                            <x-action-button 
                                type="view" 
                                :href="route('customers.show', $customer)" 
                                size="sm" 
                                class="flex-1"
                            />
                            
                            @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('customers.edit'))
                            <x-action-button 
                                type="edit" 
                                :href="route('customers.edit', $customer)" 
                                size="sm" 
                                class="flex-1"
                            />
                            @endif
                            
                            @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('customers.delete'))
                            <x-action-button 
                                type="delete" 
                                :form-action="route('customers.destroy', $customer)" 
                                size="sm" 
                                class="flex-1"
                            />
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $customers->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No customers</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating a new customer.</p>
                @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('customers.create'))
                <div class="mt-6">
                    {{-- NEW WAY: Call-to-action button --}}
                    <x-action-button type="create" :href="route('customers.create')">
                        Add Customer
                    </x-action-button>
                </div>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection

{{--
    KEY IMPROVEMENTS WITH ACTION BUTTON COMPONENT:
    
    1. LESS CODE
       - Old: ~15 lines per button
       - New: 1 line per button
       
    2. CONSISTENCY
       - All edit buttons look identical
       - All delete buttons look identical
       - Colors and icons standardized
       
    3. MAINTAINABILITY
       - Change button style in ONE place
       - Easy to add new button types
       
    4. READABILITY
       - Clear what each button does
       - No long class strings
       - Self-documenting
       
    5. FEATURES
       - Auto confirmation for delete
       - Form generation for delete
       - Responsive sizing
       - Icon-only option
       - Custom labels
--}}

@extends('layouts.app')

@section('title', 'Customers')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Customers</h1>
            <p class="mt-1 text-sm text-gray-500">Manage your customer database</p>
        </div>
        @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('customers.create'))
        <x-action-button type="create" :href="route('customers.create')">
            Add Customer
        </x-action-button>
        @endif
    </div>

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg" x-data="{ mobileOpen: false }">
        <div class="p-4 sm:p-6">
            <!-- Mobile toggle -->
            <div class="flex items-center justify-between sm:hidden">
                <span class="text-sm font-medium text-gray-700">Filters</span>
                <button type="button" @click="mobileOpen = !mobileOpen" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-filter mr-2"></i>
                    <span x-show="!mobileOpen">Show</span>
                    <span x-show="mobileOpen">Hide</span>
                </button>
            </div>
            
            <!-- Filter form -->
            <div class="mt-4 sm:mt-0" x-cloak x-show="mobileOpen || window.matchMedia('(min-width: 640px)').matches">
                <form method="GET" class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                               placeholder="Name, email, phone, company...">
                    </div>
                    
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                        <select name="type" id="type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">All Types</option>
                            <option value="individual" {{ request('type') === 'individual' ? 'selected' : '' }}>Individual</option>
                            <option value="business" {{ request('type') === 'business' ? 'selected' : '' }}>Business</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">All Status</option>
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
                                        <div class="flex items-center">
                                            <p class="text-sm font-medium text-blue-600 truncate">
                                                {{ $customer->display_name }}
                                            </p>
                                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($customer->customer_type === 'business') bg-green-100 text-green-800
                                                @else bg-blue-100 text-blue-800
                                                @endif">
                                                {{ ucfirst($customer->customer_type) }}
                                            </span>
                                        </div>
                                        <div class="mt-1 flex items-center text-sm text-gray-500">
                                            @if($customer->email)
                                                <svg class="flex-shrink-0 mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                </svg>
                                                {{ $customer->email }}
                                            @endif
                                            @if($customer->phone_number1)
                                                <svg class="flex-shrink-0 mr-1.5 h-4 w-4 ml-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                                </svg>
                                                {{ $customer->phone_number1 }}
                                            @endif
                                        </div>
                                        @if($customer->city)
                                            <div class="mt-1 text-sm text-gray-500">
                                                <svg class="inline-flex mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                {{ $customer->city }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <x-action-button type="view" :href="route('customers.show', $customer)" />
                                    
                                    @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('customers.edit'))
                                    <x-action-button type="edit" :href="route('customers.edit', $customer)" />
                                    @endif
                                    
                                    @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('customers.delete'))
                                    <x-action-button 
                                        type="delete" 
                                        :form-action="route('customers.destroy', $customer)"
                                        confirm-message="Are you sure you want to delete this customer?"
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
                                <div class="w-12 h-12 rounded-full bg-blue-500 flex items-center justify-center mr-3 flex-shrink-0">
                                    <span class="text-sm font-medium text-white">
                                        {{ strtoupper(substr($customer->display_name, 0, 1)) }}
                                    </span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h3 class="text-sm font-medium text-gray-900 truncate">{{ $customer->display_name }}</h3>
                                    <div class="flex items-center mt-1">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                            @if($customer->customer_type === 'business') bg-green-100 text-green-800
                                            @else bg-blue-100 text-blue-800
                                            @endif">
                                            {{ ucfirst($customer->customer_type) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Contact Information -->
                        <div class="space-y-2 mb-3">
                            @if($customer->email)
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-envelope w-4 h-4 mr-2 text-gray-400"></i>
                                    <span class="truncate">{{ $customer->email }}</span>
                                </div>
                            @endif
                            @if($customer->phone_number1)
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-phone w-4 h-4 mr-2 text-gray-400"></i>
                                    <span>{{ $customer->phone_number1 }}</span>
                                </div>
                            @endif
                            @if($customer->city)
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-map-marker-alt w-4 h-4 mr-2 text-gray-400"></i>
                                    <span>{{ $customer->city }}</span>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Actions -->
                        <div class="flex space-x-2 pt-3 border-t border-gray-100">
                            <x-action-button type="view" :href="route('customers.show', $customer)" size="sm" class="flex-1" />
                            
                            @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('customers.edit'))
                            <x-action-button type="edit" :href="route('customers.edit', $customer)" size="sm" class="flex-1" />
                            @endif
                            
                            @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('customers.delete'))
                            <x-action-button 
                                type="delete" 
                                :form-action="route('customers.destroy', $customer)"
                                confirm-message="Are you sure you want to delete this customer?"
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No customers</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating a new customer.</p>
                @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('customers.create'))
                <div class="mt-6">
                    <a href="{{ route('customers.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Add Customer
                    </a>
                </div>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection

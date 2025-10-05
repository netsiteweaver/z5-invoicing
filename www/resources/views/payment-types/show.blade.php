@extends('layouts.app')

@section('title', 'Payment Type Details')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <a href="{{ route('payment-types.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $paymentType->name }}</h1>
                <p class="mt-1 text-sm text-gray-500">Payment type details</p>
            </div>
        </div>
        
        <div class="flex items-center space-x-3">
            @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('payment-types.edit'))
            <a href="{{ route('payment-types.edit', $paymentType) }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="-ml-1 mr-2 fa-solid fa-edit"></i>
                Edit
            </a>
            @endif
            @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('payment-types.delete'))
            <form method="POST" action="{{ route('payment-types.destroy', $paymentType) }}" 
                  class="inline" onsubmit="return confirm('Are you sure you want to delete this payment type?')">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <i class="-ml-1 mr-2 fa-solid fa-trash"></i>
                    Delete
                </button>
            </form>
            @endif
        </div>
    </div>

    <!-- Details -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-6">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <!-- Basic Information -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Name</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $paymentType->name }}</dd>
                        </div>
                        
                        @if($paymentType->description)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Description</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $paymentType->description }}</dd>
                        </div>
                        @endif
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">UUID</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $paymentType->uuid }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Settings -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Settings</h3>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1">
                                @if($paymentType->status == 1)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Inactive
                                    </span>
                                @endif
                            </dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Default Payment Type</dt>
                            <dd class="mt-1">
                                @if($paymentType->is_default)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check mr-1"></i>
                                        Yes
                                    </span>
                                @else
                                    <span class="text-gray-500">No</span>
                                @endif
                            </dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Display Order</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $paymentType->display_order }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Usage Information -->
            @if($paymentType->payments()->count() > 0)
            <div class="mt-8 border-t border-gray-200 pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Usage</h3>
                <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                This payment type is being used in <strong>{{ $paymentType->payments()->count() }}</strong> payment(s). 
                                It cannot be deleted while in use.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Timestamps -->
            <div class="mt-8 border-t border-gray-200 pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Timestamps</h3>
                <dl class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Created</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $paymentType->created_at->format('M j, Y g:i A') }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $paymentType->updated_at->format('M j, Y g:i A') }}</dd>
                    </div>
                    
                    @if($paymentType->createdBy)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Created By</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $paymentType->createdBy->name }}</dd>
                    </div>
                    @endif
                    
                    @if($paymentType->updatedBy)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Last Updated By</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $paymentType->updatedBy->name }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection

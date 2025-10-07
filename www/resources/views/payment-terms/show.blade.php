@extends('layouts.app')

@section('title', 'Payment Term Details')

@section('content')
<div class="bg-white shadow rounded-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">{{ $payment_term->name }}</h1>
        <div class="flex space-x-3">
            <x-action-button type="edit" :href="route('payment-terms.edit', $payment_term)" />
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div class="bg-gray-50 rounded-lg p-4">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
            <dl class="space-y-3">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Name</dt>
                    <dd class="text-sm text-gray-900">{{ $payment_term->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Type</dt>
                    <dd class="text-sm text-gray-900">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ strtoupper($payment_term->type) }}
                        </span>
                    </dd>
                </div>
                @if($payment_term->type === 'days')
                <div>
                    <dt class="text-sm font-medium text-gray-500">Days</dt>
                    <dd class="text-sm text-gray-900">{{ $payment_term->days }} days</dd>
                </div>
                @endif
                <div>
                    <dt class="text-sm font-medium text-gray-500">Default</dt>
                    <dd class="text-sm text-gray-900">
                        @if($payment_term->is_default)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Yes
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                No
                            </span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd class="text-sm text-gray-900">
                        @if($payment_term->status)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Active
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                Inactive
                            </span>
                        @endif
                    </dd>
                </div>
            </dl>
        </div>

        @if($payment_term->description)
        <div class="bg-gray-50 rounded-lg p-4">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Description</h3>
            <p class="text-sm text-gray-700">{{ $payment_term->description }}</p>
        </div>
        @endif
    </div>

    <div class="mt-6 pt-6 border-t border-gray-200">
        <div class="flex justify-between items-center">
            <div class="text-sm text-gray-500">
                Created: {{ $payment_term->created_at->format('M d, Y') }}
                @if($payment_term->updated_at != $payment_term->created_at)
                    | Updated: {{ $payment_term->updated_at->format('M d, Y') }}
                @endif
            </div>
            <x-action-button 
                type="delete" 
                :form-action="route('payment-terms.destroy', $payment_term)"
                confirm-message="Are you sure you want to delete this payment term?"
            />
        </div>
    </div>
</div>
@endsection

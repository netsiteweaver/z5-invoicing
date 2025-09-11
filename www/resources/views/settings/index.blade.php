@extends('layouts.app')

@section('title', $pageTitle ?? 'Company Settings')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Company Settings</h1>
            <p class="mt-1 text-sm text-gray-500">Manage your company information and preferences</p>
        </div>
        @if($settings)
            <a href="{{ route('settings.edit', $settings->id) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fas fa-edit mr-2 h-4 w-4"></i>
                Edit Settings
            </a>
        @else
            <a href="{{ route('settings.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                <i class="fas fa-plus mr-2 h-4 w-4"></i>
                Create Settings
            </a>
        @endif
    </div>

    @if(session('success'))
        <div class="rounded-md bg-green-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if($settings)
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-8">
                <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                    <!-- Company Logo and Basic Info -->
                    <div class="lg:col-span-1">
                        <div class="text-center">
                            @if($settings->logo_path)
                                <img src="{{ Storage::disk('public')->url($settings->logo_path) }}" 
                                     alt="Company Logo" 
                                     class="mx-auto h-32 w-32 object-cover rounded-lg border border-gray-200">
                            @else
                                <div class="mx-auto h-32 w-32 bg-gray-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-building text-4xl text-gray-400"></i>
                                </div>
                            @endif
                            <h3 class="mt-4 text-xl font-semibold text-gray-900">{{ $settings->display_name }}</h3>
                            @if($settings->description)
                                <p class="mt-2 text-sm text-gray-600">{{ $settings->description }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="lg:col-span-2">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4">Contact Information</h4>
                                <dl class="space-y-3">
                                    @if($settings->address)
                                        <div class="flex">
                                            <dt class="flex-shrink-0">
                                                <i class="fas fa-map-marker-alt text-gray-400 mr-3 mt-0.5"></i>
                                            </dt>
                                            <dd class="text-sm text-gray-900">{{ $settings->full_address }}</dd>
                                        </div>
                                    @endif
                                    @if($settings->phone_primary)
                                        <div class="flex">
                                            <dt class="flex-shrink-0">
                                                <i class="fas fa-phone text-gray-400 mr-3 mt-0.5"></i>
                                            </dt>
                                            <dd class="text-sm text-gray-900">{{ $settings->phone_primary }}</dd>
                                        </div>
                                    @endif
                                    @if($settings->phone_secondary)
                                        <div class="flex">
                                            <dt class="flex-shrink-0">
                                                <i class="fas fa-phone text-gray-400 mr-3 mt-0.5"></i>
                                            </dt>
                                            <dd class="text-sm text-gray-900">{{ $settings->phone_secondary }}</dd>
                                        </div>
                                    @endif
                                    @if($settings->email_primary)
                                        <div class="flex">
                                            <dt class="flex-shrink-0">
                                                <i class="fas fa-envelope text-gray-400 mr-3 mt-0.5"></i>
                                            </dt>
                                            <dd class="text-sm text-gray-900">{{ $settings->email_primary }}</dd>
                                        </div>
                                    @endif
                                    @if($settings->email_secondary)
                                        <div class="flex">
                                            <dt class="flex-shrink-0">
                                                <i class="fas fa-envelope text-gray-400 mr-3 mt-0.5"></i>
                                            </dt>
                                            <dd class="text-sm text-gray-900">{{ $settings->email_secondary }}</dd>
                                        </div>
                                    @endif
                                    @if($settings->website)
                                        <div class="flex">
                                            <dt class="flex-shrink-0">
                                                <i class="fas fa-globe text-gray-400 mr-3 mt-0.5"></i>
                                            </dt>
                                            <dd class="text-sm text-gray-900">
                                                <a href="{{ $settings->website }}" target="_blank" class="text-blue-600 hover:text-blue-500">
                                                    {{ $settings->website }}
                                                </a>
                                            </dd>
                                        </div>
                                    @endif
                                </dl>
                            </div>

                            <div>
                                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4">Business Information</h4>
                                <dl class="space-y-3">
                                    @if($settings->brn)
                                        <div class="flex">
                                            <dt class="flex-shrink-0 w-20 text-sm font-medium text-gray-500">BRN:</dt>
                                            <dd class="text-sm text-gray-900">{{ $settings->brn }}</dd>
                                        </div>
                                    @endif
                                    @if($settings->vat_number)
                                        <div class="flex">
                                            <dt class="flex-shrink-0 w-20 text-sm font-medium text-gray-500">VAT:</dt>
                                            <dd class="text-sm text-gray-900">{{ $settings->vat_number }}</dd>
                                        </div>
                                    @endif
                                    <div class="flex">
                                        <dt class="flex-shrink-0 w-20 text-sm font-medium text-gray-500">Currency:</dt>
                                        <dd class="text-sm text-gray-900">{{ $settings->currency }}</dd>
                                    </div>
                                    <div class="flex">
                                        <dt class="flex-shrink-0 w-20 text-sm font-medium text-gray-500">Timezone:</dt>
                                        <dd class="text-sm text-gray-900">{{ $settings->timezone }}</dd>
                                    </div>
                                    <div class="flex">
                                        <dt class="flex-shrink-0 w-20 text-sm font-medium text-gray-500">Date:</dt>
                                        <dd class="text-sm text-gray-900">{{ $settings->date_format }}</dd>
                                    </div>
                                    <div class="flex">
                                        <dt class="flex-shrink-0 w-20 text-sm font-medium text-gray-500">Time:</dt>
                                        <dd class="text-sm text-gray-900">{{ $settings->time_format }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-12">
            <i class="fas fa-building text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No Company Settings Found</h3>
            <p class="text-sm text-gray-500 mb-6">Please create your company settings to get started.</p>
            <a href="{{ route('settings.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fas fa-plus mr-2 h-4 w-4"></i>
                Create Company Settings
            </a>
        </div>
    @endif
</div>
@endsection
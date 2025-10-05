@extends('layouts.app')

@section('title', 'Alerts & Warnings')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Alerts & Warnings</h2>
                    <p class="mt-1 text-sm text-gray-600">System alerts and recommendations for your business operations</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('reports.index') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Reports
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Summary -->
    @if($alerts->count() > 0)
        @php
            $criticalCount = $alerts->where('severity', 'critical')->count();
            $warningCount = $alerts->where('severity', 'warning')->count();
            $infoCount = $alerts->where('severity', 'info')->count();
        @endphp
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @if($criticalCount > 0)
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-exclamation-circle text-red-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-red-800">Critical Alerts</h3>
                        <p class="text-2xl font-bold text-red-600">{{ $criticalCount }}</p>
                        <p class="text-sm text-red-600">Requires immediate attention</p>
                    </div>
                </div>
            </div>
            @endif

            @if($warningCount > 0)
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-yellow-800">Warnings</h3>
                        <p class="text-2xl font-bold text-yellow-600">{{ $warningCount }}</p>
                        <p class="text-sm text-yellow-600">Needs attention soon</p>
                    </div>
                </div>
            </div>
            @endif

            @if($infoCount > 0)
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-info-circle text-blue-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-blue-800">Information</h3>
                        <p class="text-2xl font-bold text-blue-600">{{ $infoCount }}</p>
                        <p class="text-sm text-blue-600">For your awareness</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    @endif

    <!-- Alerts List -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">All Alerts</h3>
        </div>
        <div class="divide-y divide-gray-200">
            @if($alerts->count() > 0)
                @foreach($alerts as $alert)
                <div class="px-6 py-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center
                                @if($alert['severity'] === 'critical') bg-red-100
                                @elseif($alert['severity'] === 'warning') bg-yellow-100
                                @else bg-blue-100
                                @endif">
                                <i class="{{ $alert['icon'] }} text-sm
                                    @if($alert['severity'] === 'critical') text-red-600
                                    @elseif($alert['severity'] === 'warning') text-yellow-600
                                    @else text-blue-600
                                    @endif"></i>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <h4 class="text-sm font-medium text-gray-900">{{ $alert['title'] }}</h4>
                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($alert['severity'] === 'critical') bg-red-100 text-red-800
                                        @elseif($alert['severity'] === 'warning') bg-yellow-100 text-yellow-800
                                        @else bg-blue-100 text-blue-800
                                        @endif">
                                        {{ ucfirst($alert['severity']) }}
                                    </span>
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $alert['date']->diffForHumans() }}
                                </div>
                            </div>
                            <p class="mt-1 text-sm text-gray-600">{{ $alert['message'] }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <div class="px-6 py-12 text-center">
                    <div class="w-16 h-16 mx-auto bg-green-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">All Good!</h3>
                    <p class="text-gray-500">No alerts or warnings at this time. Your business operations are running smoothly.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Alert Types Legend -->
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Alert Types</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="flex items-center p-3 bg-red-50 rounded-lg">
                <div class="w-6 h-6 bg-red-100 rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-exclamation-circle text-red-600 text-sm"></i>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-red-800">Critical</h4>
                    <p class="text-xs text-red-600">Requires immediate action</p>
                </div>
            </div>
            
            <div class="flex items-center p-3 bg-yellow-50 rounded-lg">
                <div class="w-6 h-6 bg-yellow-100 rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-exclamation-triangle text-yellow-600 text-sm"></i>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-yellow-800">Warning</h4>
                    <p class="text-xs text-yellow-600">Needs attention soon</p>
                </div>
            </div>
            
            <div class="flex items-center p-3 bg-blue-50 rounded-lg">
                <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-info-circle text-blue-600 text-sm"></i>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-blue-800">Information</h4>
                    <p class="text-xs text-blue-600">For your awareness</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recommendations -->
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Recommendations</h3>
        <div class="space-y-4">
            @if($criticalCount > 0 || $warningCount > 0)
                <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-lightbulb text-yellow-600"></i>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-yellow-800">Address Critical and Warning Items</h4>
                            <p class="text-sm text-yellow-700 mt-1">
                                Review and take action on critical alerts and warnings to maintain optimal business operations.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-chart-line text-blue-600"></i>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-blue-800">Regular Monitoring</h4>
                        <p class="text-sm text-blue-700 mt-1">
                            Check this alerts page regularly to stay on top of potential issues before they become problems.
                        </p>
                    </div>
                </div>
            </div>

            <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-cog text-green-600"></i>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-green-800">System Optimization</h4>
                        <p class="text-sm text-green-700 mt-1">
                            Use the insights from these alerts to optimize your inventory levels, payment processes, and order management.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

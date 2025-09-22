@extends('layouts.app')

@section('title', 'UOM Details')

@section('content')
<div class="bg-white shadow rounded-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">{{ $uom->name }}</h1>
        <div class="flex space-x-3">
            <a href="{{ route('uoms.edit', $uom) }}" class="btn btn-edit">
                <i class="btn-icon fa-solid fa-pen"></i>
                Edit
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div class="bg-gray-50 rounded-lg p-4">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
            <dl class="space-y-3">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Name</dt>
                    <dd class="text-sm text-gray-900">{{ $uom->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Code</dt>
                    <dd class="text-sm text-gray-900">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $uom->code }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Units per UOM</dt>
                    <dd class="text-sm text-gray-900">{{ $uom->units_per_uom }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd class="text-sm text-gray-900">
                        @if($uom->status)
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

        @if($uom->description)
        <div class="bg-gray-50 rounded-lg p-4">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Description</h3>
            <p class="text-sm text-gray-700">{{ $uom->description }}</p>
        </div>
        @endif
    </div>

    <div class="mt-6 pt-6 border-t border-gray-200">
        <div class="flex justify-between items-center">
            <div class="text-sm text-gray-500">
                Created: {{ $uom->created_at->format('M d, Y') }}
                @if($uom->updated_at != $uom->created_at)
                    | Updated: {{ $uom->updated_at->format('M d, Y') }}
                @endif
            </div>
            <form method="POST" action="{{ route('uoms.destroy', $uom) }}" class="inline" onsubmit="return confirm('Are you sure you want to deactivate this UOM?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-delete">
                    <i class="btn-icon fa-solid fa-trash"></i>
                    Deactivate
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

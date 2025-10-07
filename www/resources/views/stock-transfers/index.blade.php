@extends('layouts.app')

@section('title', 'Stock Transfers')

@section('actions')
<x-action-button type="create" :href="route('stock-transfers.create')">
    New Transfer
</x-action-button>
@endsection

@section('content')
<div class="bg-white shadow rounded-lg">
  <div class="px-4 py-5 sm:p-6">
    <!-- Desktop Table -->
    <div class="hidden sm:block overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transfer #</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">From</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">To</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          @forelse($transfers as $transfer)
          <tr class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap">{{ $transfer->transfer_number }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ optional($transfer->transfer_date instanceof \Carbon\Carbon ? $transfer->transfer_date : \Carbon\Carbon::parse($transfer->transfer_date))->format('Y-m-d') }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ $transfer->fromDepartment->name ?? '-' }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ $transfer->toDepartment->name ?? '-' }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ ucfirst(str_replace('_', ' ', $transfer->status)) }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
              <div class="flex space-x-2">
                <x-action-button type="view" :href="route('stock-transfers.show', $transfer)" />
                
                @if($transfer->status !== 'received')
                <x-action-button type="edit" :href="route('stock-transfers.edit', $transfer)" />
                @endif
                
                @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('stock_transfers.delete'))
                <x-action-button 
                  type="delete" 
                  :form-action="route('stock-transfers.destroy', $transfer)"
                  confirm-message="Delete this transfer? This will reverse inventory if already received."
                />
                @endif
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
              <div class="flex flex-col items-center space-y-4">
                <p>No transfers found.</p>
                <x-action-button type="create" :href="route('stock-transfers.create')">
                  Create Transfer
                </x-action-button>
              </div>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    
    <!-- Mobile Cards -->
    <div class="sm:hidden space-y-4">
      @forelse($transfers as $transfer)
        <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
          <!-- Header -->
          <div class="flex items-start justify-between mb-3">
            <div class="min-w-0 flex-1">
              <h3 class="text-sm font-medium text-gray-900 truncate">{{ $transfer->transfer_number }}</h3>
              <p class="text-xs text-gray-500">{{ optional($transfer->transfer_date instanceof \Carbon\Carbon ? $transfer->transfer_date : \Carbon\Carbon::parse($transfer->transfer_date))->format('M d, Y') }}</p>
            </div>
            <div class="ml-2 flex-shrink-0">
              @if($transfer->status === 'received')
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                  Received
                </span>
              @elseif($transfer->status === 'pending')
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                  Pending
                </span>
              @else
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                  {{ ucfirst(str_replace('_', ' ', $transfer->status)) }}
                </span>
              @endif
            </div>
          </div>
          
          <!-- Transfer Details -->
          <div class="grid grid-cols-1 gap-2 mb-3">
            <div>
              <p class="text-xs text-gray-500">From</p>
              <p class="text-sm text-gray-900">{{ $transfer->fromDepartment->name ?? '-' }}</p>
            </div>
            <div>
              <p class="text-xs text-gray-500">To</p>
              <p class="text-sm text-gray-900">{{ $transfer->toDepartment->name ?? '-' }}</p>
            </div>
          </div>
          
          <!-- Actions -->
          <div class="flex space-x-2 pt-3 border-t border-gray-100">
            <a href="{{ route('stock-transfers.show', $transfer) }}" 
               class="flex-1 inline-flex justify-center items-center px-3 py-2 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
              <i class="fas fa-eye mr-1"></i>
              View
            </a>
            @if($transfer->status !== 'received')
              <a href="{{ route('stock-transfers.edit', $transfer) }}" 
                 class="flex-1 inline-flex justify-center items-center px-3 py-2 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fas fa-edit mr-1"></i>
                Edit
              </a>
            @endif
            @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('stock_transfers.delete'))
              <form method="POST" action="{{ route('stock-transfers.destroy', $transfer) }}" onsubmit="return confirm('Delete this transfer? This will reverse inventory if already received.');" class="flex-1">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full inline-flex justify-center items-center px-3 py-2 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                  <i class="fas fa-trash mr-1"></i>
                  Delete
                </button>
              </form>
            @endif
          </div>
        </div>
      @empty
        <div class="text-center py-12">
          <i class="fas fa-exchange-alt text-6xl text-gray-400 mb-4"></i>
          <h3 class="mt-2 text-sm font-medium text-gray-900">No transfers found</h3>
          <p class="mt-1 text-sm text-gray-500">Get started by creating a new stock transfer.</p>
          <div class="mt-6">
            <a href="{{ route('stock-transfers.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
              <i class="fas fa-plus mr-2"></i>
              Create Transfer
            </a>
          </div>
        </div>
      @endforelse
    </div>
  </div>
  <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
    {{ $transfers->links() }}
  </div>
</div>
@endsection



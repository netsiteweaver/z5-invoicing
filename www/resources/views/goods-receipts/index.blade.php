@extends('layouts.app')

@section('title', 'Goods Receipts')
@section('description', 'List of received goods (GRN)')

@section('actions')
<a href="{{ route('goods-receipts.create') }}" class="btn btn-create">
    <i class="btn-icon fa-solid fa-plus"></i>
    New Receipt
</a>
@endsection

@section('content')
<div class="bg-white shadow rounded-lg">
  <div class="px-4 py-5 sm:p-6">
    <!-- Desktop Table -->
    <div class="hidden sm:block overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">GRN #</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          @forelse($receipts as $receipt)
          <tr class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap">{{ $receipt->grn_number }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ optional($receipt->receipt_date instanceof \Carbon\Carbon ? $receipt->receipt_date : \Carbon\Carbon::parse($receipt->receipt_date))->format('Y-m-d') }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ $receipt->department->name ?? '-' }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ $receipt->supplier->name ?? ($receipt->supplier_name ?? '-') }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
              <div class="flex space-x-2">
                <a href="{{ route('goods-receipts.show', $receipt) }}" class="btn btn-view">
                  <i class="btn-icon fa-regular fa-eye"></i>
                  View
                </a>
                @if(($receipt->approval_status ?? 'submitted') !== 'approved')
                <a href="{{ route('goods-receipts.edit', $receipt) }}" class="btn btn-edit">
                  <i class="btn-icon fa-solid fa-pen"></i>
                  Edit
                </a>
                @endif
                @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('goods_receipts.delete'))
                <form method="POST" action="{{ route('goods-receipts.destroy', $receipt) }}" onsubmit="return confirm('Delete this goods receipt? This will reverse inventory if approved.');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-delete">
                    <i class="btn-icon fa-solid fa-trash"></i>
                    Delete
                  </button>
                </form>
                @endif
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
              <div class="flex flex-col items-center space-y-4">
                <p>No receipts found.</p>
                <a href="{{ route('goods-receipts.create') }}" class="btn btn-create">
                  <i class="btn-icon fa-solid fa-plus"></i>
                  Create Receipt
                </a>
              </div>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    
    <!-- Mobile Cards -->
    <div class="sm:hidden space-y-4">
      @forelse($receipts as $receipt)
        <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
          <!-- Header -->
          <div class="flex items-start justify-between mb-3">
            <div class="min-w-0 flex-1">
              <h3 class="text-sm font-medium text-gray-900 truncate">{{ $receipt->grn_number }}</h3>
              <p class="text-xs text-gray-500">{{ optional($receipt->receipt_date instanceof \Carbon\Carbon ? $receipt->receipt_date : \Carbon\Carbon::parse($receipt->receipt_date))->format('M d, Y') }}</p>
            </div>
            <div class="ml-2 flex-shrink-0">
              @if(($receipt->approval_status ?? 'submitted') === 'approved')
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                  Approved
                </span>
              @else
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                  Pending
                </span>
              @endif
            </div>
          </div>
          
          <!-- Details -->
          <div class="grid grid-cols-1 gap-2 mb-3">
            <div>
              <p class="text-xs text-gray-500">Location</p>
              <p class="text-sm text-gray-900">{{ $receipt->department->name ?? '-' }}</p>
            </div>
            <div>
              <p class="text-xs text-gray-500">Supplier</p>
              <p class="text-sm text-gray-900">{{ $receipt->supplier->name ?? ($receipt->supplier_name ?? '-') }}</p>
            </div>
          </div>
          
          <!-- Actions -->
          <div class="flex space-x-2 pt-3 border-t border-gray-100">
            <a href="{{ route('goods-receipts.show', $receipt) }}" 
               class="flex-1 inline-flex justify-center items-center px-3 py-2 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
              <i class="fas fa-eye mr-1"></i>
              View
            </a>
            @if(($receipt->approval_status ?? 'submitted') !== 'approved')
              <a href="{{ route('goods-receipts.edit', $receipt) }}" 
                 class="flex-1 inline-flex justify-center items-center px-3 py-2 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fas fa-edit mr-1"></i>
                Edit
              </a>
            @endif
            @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('goods_receipts.delete'))
              <form method="POST" action="{{ route('goods-receipts.destroy', $receipt) }}" onsubmit="return confirm('Delete this goods receipt? This will reverse inventory if approved.');" class="flex-1">
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
          <i class="fas fa-receipt text-6xl text-gray-400 mb-4"></i>
          <h3 class="mt-2 text-sm font-medium text-gray-900">No receipts found</h3>
          <p class="mt-1 text-sm text-gray-500">Get started by creating a new goods receipt.</p>
          <div class="mt-6">
            <a href="{{ route('goods-receipts.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
              <i class="fas fa-plus mr-2"></i>
              Create Receipt
            </a>
          </div>
        </div>
      @endforelse
    </div>
  </div>
  <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
    {{ $receipts->links() }}
  </div>
</div>
@endsection

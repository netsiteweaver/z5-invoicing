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
    <div class="overflow-x-auto">
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
  </div>
  <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
    {{ $receipts->links() }}
  </div>
</div>
@endsection

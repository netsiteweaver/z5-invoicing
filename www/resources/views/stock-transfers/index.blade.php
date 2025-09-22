@extends('layouts.app')

@section('title', 'Stock Transfers')

@section('actions')
<a href="{{ route('stock-transfers.create') }}" class="btn btn-create">
    <i class="btn-icon fa-solid fa-plus"></i>
    New Transfer
</a>
@endsection

@section('content')
<div class="bg-white shadow rounded-lg">
  <div class="px-4 py-5 sm:p-6">
    <div class="overflow-x-auto">
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
                <a href="{{ route('stock-transfers.show', $transfer) }}" class="btn btn-view">
                  <i class="btn-icon fa-regular fa-eye"></i>
                  View
                </a>
                <a href="{{ route('stock-transfers.edit', $transfer) }}" class="btn btn-edit">
                  <i class="btn-icon fa-solid fa-pen"></i>
                  Edit
                </a>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
              <div class="flex flex-col items-center space-y-4">
                <p>No transfers found.</p>
                <a href="{{ route('stock-transfers.create') }}" class="btn btn-create">
                  <i class="btn-icon fa-solid fa-plus"></i>
                  Create Transfer
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
    {{ $transfers->links() }}
  </div>
</div>
@endsection



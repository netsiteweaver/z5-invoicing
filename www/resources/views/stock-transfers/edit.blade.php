@extends('layouts.app')

@section('title', 'Edit Stock Transfer ' . $transfer->transfer_number)

@section('content')
<form method="POST" action="{{ route('stock-transfers.update', $transfer) }}" class="bg-white shadow rounded-lg">
  @csrf
  @method('PUT')
  <div class="px-4 py-5 sm:p-6 space-y-6">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div>
        <label class="block text-sm font-medium text-gray-700">Status</label>
        <select name="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
          @foreach(['draft','requested','approved','in_transit','received','cancelled'] as $s)
          <option value="{{ $s }}" {{ $transfer->status === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
          @endforeach
        </select>
      </div>
      <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-gray-700">Notes</label>
        <textarea name="notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ $transfer->notes }}</textarea>
      </div>
    </div>
  </div>
  <div class="bg-gray-50 px-4 py-3 sm:px-6 text-right">
    <a href="{{ route('stock-transfers.show', $transfer) }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm bg-white hover:bg-gray-50">Cancel</a>
    <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">Save Changes</button>
  </div>
</form>
@endsection



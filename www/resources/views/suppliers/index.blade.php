@extends('layouts.app')

@section('title', 'Suppliers')
@section('description', 'Manage suppliers')

@section('actions')
<a href="{{ route('suppliers.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
    <i class="fas fa-plus mr-2"></i> New Supplier
</a>
@endsection

@section('content')
<div class="bg-white shadow rounded-lg">
  <div class="px-4 py-5 sm:p-6">
    <form method="GET" class="mb-4 flex space-x-2">
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Search suppliers..." class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
      <button class="px-4 py-2 border border-gray-300 rounded-md text-sm bg-white hover:bg-gray-50">Search</button>
    </form>
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          @forelse($suppliers as $supplier)
          <tr class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap">
              <a href="{{ route('suppliers.show', $supplier) }}" class="text-blue-600 hover:underline">{{ $supplier->name }}</a>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">{{ $supplier->contact_person ?? '-' }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ $supplier->email ?? '-' }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ $supplier->phone_number1 ?? '-' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
              <a href="{{ route('suppliers.edit', $supplier) }}" class="text-yellow-600 hover:text-yellow-900"><i class="fas fa-edit"></i></a>
              <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="inline" onsubmit="return confirm('Delete this supplier?')">
                @csrf
                @method('DELETE')
                <button class="text-red-600 hover:text-red-800 ml-3"><i class="fas fa-trash"></i></button>
              </form>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No suppliers found.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
    {{ $suppliers->withQueryString()->links() }}
  </div>
@endsection


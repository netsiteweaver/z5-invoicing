@extends('layouts.app')

@section('title', 'Payment Types')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Payment Types</h1>
            <p class="mt-1 text-sm text-gray-500">Manage payment types for orders and sales</p>
        </div>
        @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('payment-types.create'))
        <a href="{{ route('payment-types.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <i class="-ml-1 mr-2 fa-solid fa-plus"></i>
            Add Payment Type
        </a>
        @endif
    </div>

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg p-6">
        <form method="GET" class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" 
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                       placeholder="Name, description..." autofocus>
            </div>
            
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="">All Status</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            
            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="-ml-1 mr-2 fa-solid fa-magnifying-glass"></i>
                    Filter
                </button>
                <a href="{{ route('payment-types.index') }}" class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="-ml-1 mr-2 fa-solid fa-xmark"></i>
                    Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Payment Types Table -->
    @if($paymentTypes->count() > 0)
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-3 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-grip-vertical text-gray-400 mr-2"></i>
                        <span class="text-sm text-gray-600">Drag rows to reorder</span>
                    </div>
                    <button type="button" id="save-order-btn" class="hidden inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-save mr-1"></i>
                        Save Order
                    </button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="sortable-table">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="w-8 px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-grip-vertical"></i>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Name
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Description
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Default
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Order
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Created
                            </th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($paymentTypes as $paymentType)
                        <tr class="hover:bg-gray-50 sortable-row" data-id="{{ $paymentType->id }}">
                            <td class="px-2 py-4 whitespace-nowrap cursor-move drag-handle">
                                <i class="fas fa-grip-vertical text-gray-400"></i>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $paymentType->name }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-500">
                                    {{ $paymentType->description ? Str::limit($paymentType->description, 50) : '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($paymentType->is_default)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check mr-1"></i>
                                        Default
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $paymentType->display_order }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($paymentType->status == 1)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $paymentType->created_at->format('M j, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('payment-types.show', $paymentType) }}" 
                                       class="text-blue-600 hover:text-blue-900" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('payment-types.edit'))
                                    <a href="{{ route('payment-types.edit', $paymentType) }}" 
                                       class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endif
                                    @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('payment-types.delete'))
                                    <form method="POST" action="{{ route('payment-types.destroy', $paymentType) }}" 
                                          class="inline" onsubmit="return confirm('Are you sure you want to delete this payment type?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $paymentTypes->links() }}
            </div>
        </div>
    @else
        <div class="bg-white shadow rounded-lg p-6">
            <div class="text-center">
                <i class="fas fa-credit-card text-gray-400 text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No payment types found</h3>
                <p class="text-gray-500 mb-4">
                    @if(request()->hasAny(['search', 'status']))
                        Try adjusting your search criteria.
                    @else
                        Get started by creating your first payment type.
                    @endif
                </p>
                @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('payment-types.create'))
                <a href="{{ route('payment-types.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="-ml-1 mr-2 fa-solid fa-plus"></i>
                    Add Payment Type
                </a>
                @endif
            </div>
        </div>
    @endif
</div>

<style>
.sortable-row {
    transition: all 0.3s ease;
}

.sortable-row.dragging {
    opacity: 0.5;
    transform: rotate(2deg);
}

.sortable-row.drag-over {
    border-top: 2px solid #3b82f6;
}

.drag-handle {
    user-select: none;
}

.drag-handle:hover {
    color: #6b7280;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const table = document.getElementById('sortable-table');
    const tbody = table.querySelector('tbody');
    const saveBtn = document.getElementById('save-order-btn');
    let hasChanges = false;

    // Initialize Sortable
    const sortable = Sortable.create(tbody, {
        handle: '.drag-handle',
        animation: 150,
        ghostClass: 'sorting',
        chosenClass: 'chosen',
        dragClass: 'dragging',
        onStart: function(evt) {
            evt.item.classList.add('dragging');
        },
        onEnd: function(evt) {
            evt.item.classList.remove('dragging');
            hasChanges = true;
            saveBtn.classList.remove('hidden');
        },
        onMove: function(evt, originalEvent) {
            // Prevent dropping on action buttons
            if (originalEvent.target.closest('a, button, form')) {
                return false;
            }
        }
    });

    // Save order function
    saveBtn.addEventListener('click', function() {
        const rows = tbody.querySelectorAll('.sortable-row');
        const order = Array.from(rows).map((row, index) => ({
            id: row.dataset.id,
            display_order: index + 1
        }));

        // Show loading state
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Saving...';
        saveBtn.disabled = true;

        fetch('{{ route("payment-types.update-order") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ order: order })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                showNotification('Order saved successfully!', 'success');
                hasChanges = false;
                saveBtn.classList.add('hidden');
                
                // Reload page to show updated order
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                throw new Error(data.message || 'Failed to save order');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Failed to save order: ' + error.message, 'error');
        })
        .finally(() => {
            // Reset button state
            saveBtn.innerHTML = '<i class="fas fa-save mr-1"></i>Save Order';
            saveBtn.disabled = false;
        });
    });

    // Prevent form submission if there are unsaved changes
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            if (hasChanges) {
                if (!confirm('You have unsaved changes to the payment type order. Do you want to continue without saving?')) {
                    e.preventDefault();
                }
            }
        });
    });

    // Notification function
    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-md shadow-lg text-white ${
            type === 'success' ? 'bg-green-500' : 'bg-red-500'
        }`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
});
</script>
@endsection

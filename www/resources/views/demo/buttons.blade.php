@extends('layouts.app')

@section('title', 'Button Component Demo')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Action Button Component Demo</h1>
        <p class="mt-2 text-sm text-gray-600">
            A comprehensive demonstration of all available button types with consistent styling.
        </p>
    </div>

    <!-- Standard Size Buttons -->
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Standard Buttons (Medium Size)</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            <div class="space-y-2">
                <p class="text-xs font-medium text-gray-500 uppercase">Create</p>
                <x-action-button type="create" href="#" />
            </div>
            <div class="space-y-2">
                <p class="text-xs font-medium text-gray-500 uppercase">Edit</p>
                <x-action-button type="edit" href="#" />
            </div>
            <div class="space-y-2">
                <p class="text-xs font-medium text-gray-500 uppercase">Delete</p>
                <x-action-button type="delete" form-action="#" />
            </div>
            <div class="space-y-2">
                <p class="text-xs font-medium text-gray-500 uppercase">View</p>
                <x-action-button type="view" href="#" />
            </div>
            <div class="space-y-2">
                <p class="text-xs font-medium text-gray-500 uppercase">Save</p>
                <x-action-button type="save" />
            </div>
            <div class="space-y-2">
                <p class="text-xs font-medium text-gray-500 uppercase">Cancel</p>
                <x-action-button type="cancel" />
            </div>
            <div class="space-y-2">
                <p class="text-xs font-medium text-gray-500 uppercase">Back</p>
                <x-action-button type="back" href="#" />
            </div>
            <div class="space-y-2">
                <p class="text-xs font-medium text-gray-500 uppercase">Print</p>
                <x-action-button type="print" />
            </div>
            <div class="space-y-2">
                <p class="text-xs font-medium text-gray-500 uppercase">Export</p>
                <x-action-button type="export" />
            </div>
            <div class="space-y-2">
                <p class="text-xs font-medium text-gray-500 uppercase">Filter</p>
                <x-action-button type="filter" />
            </div>
            <div class="space-y-2">
                <p class="text-xs font-medium text-gray-500 uppercase">Search</p>
                <x-action-button type="search" />
            </div>
            <div class="space-y-2">
                <p class="text-xs font-medium text-gray-500 uppercase">Approve</p>
                <x-action-button type="approve" />
            </div>
            <div class="space-y-2">
                <p class="text-xs font-medium text-gray-500 uppercase">Reject</p>
                <x-action-button type="reject" />
            </div>
            <div class="space-y-2">
                <p class="text-xs font-medium text-gray-500 uppercase">Submit</p>
                <x-action-button type="submit" />
            </div>
            <div class="space-y-2">
                <p class="text-xs font-medium text-gray-500 uppercase">Download</p>
                <x-action-button type="download" />
            </div>
            <div class="space-y-2">
                <p class="text-xs font-medium text-gray-500 uppercase">Upload</p>
                <x-action-button type="upload" />
            </div>
            <div class="space-y-2">
                <p class="text-xs font-medium text-gray-500 uppercase">Send</p>
                <x-action-button type="send" />
            </div>
            <div class="space-y-2">
                <p class="text-xs font-medium text-gray-500 uppercase">Copy</p>
                <x-action-button type="copy" />
            </div>
            <div class="space-y-2">
                <p class="text-xs font-medium text-gray-500 uppercase">Share</p>
                <x-action-button type="share" />
            </div>
            <div class="space-y-2">
                <p class="text-xs font-medium text-gray-500 uppercase">Refresh</p>
                <x-action-button type="refresh" />
            </div>
            <div class="space-y-2">
                <p class="text-xs font-medium text-gray-500 uppercase">Add</p>
                <x-action-button type="add" />
            </div>
            <div class="space-y-2">
                <p class="text-xs font-medium text-gray-500 uppercase">Remove</p>
                <x-action-button type="remove" />
            </div>
            <div class="space-y-2">
                <p class="text-xs font-medium text-gray-500 uppercase">Reset</p>
                <x-action-button type="reset" />
            </div>
            <div class="space-y-2">
                <p class="text-xs font-medium text-gray-500 uppercase">Settings</p>
                <x-action-button type="settings" href="#" />
            </div>
        </div>
    </div>

    <!-- Button Sizes -->
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Button Sizes</h2>
        <div class="space-y-4">
            <div class="flex items-center space-x-4">
                <span class="w-24 text-sm text-gray-600">Small:</span>
                <x-action-button type="edit" href="#" size="sm" />
                <x-action-button type="delete" form-action="#" size="sm" />
                <x-action-button type="save" size="sm" />
            </div>
            <div class="flex items-center space-x-4">
                <span class="w-24 text-sm text-gray-600">Medium:</span>
                <x-action-button type="edit" href="#" size="md" />
                <x-action-button type="delete" form-action="#" size="md" />
                <x-action-button type="save" size="md" />
            </div>
            <div class="flex items-center space-x-4">
                <span class="w-24 text-sm text-gray-600">Large:</span>
                <x-action-button type="edit" href="#" size="lg" />
                <x-action-button type="delete" form-action="#" size="lg" />
                <x-action-button type="save" size="lg" />
            </div>
        </div>
    </div>

    <!-- Icon Only Buttons -->
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Icon Only Buttons</h2>
        <div class="flex flex-wrap gap-2">
            <x-action-button type="create" href="#" :icon-only="true" />
            <x-action-button type="edit" href="#" :icon-only="true" />
            <x-action-button type="delete" form-action="#" :icon-only="true" />
            <x-action-button type="view" href="#" :icon-only="true" />
            <x-action-button type="save" :icon-only="true" />
            <x-action-button type="print" :icon-only="true" />
            <x-action-button type="download" :icon-only="true" />
            <x-action-button type="share" :icon-only="true" />
        </div>
    </div>

    <!-- Custom Labels -->
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Custom Labels</h2>
        <div class="flex flex-wrap gap-2">
            <x-action-button type="create" href="#">Add New Customer</x-action-button>
            <x-action-button type="edit" href="#">Update Profile</x-action-button>
            <x-action-button type="save">Save Changes</x-action-button>
            <x-action-button type="export">Export to Excel</x-action-button>
        </div>
    </div>

    <!-- Custom Button -->
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Custom Button</h2>
        <div class="flex flex-wrap gap-2">
            <x-action-button 
                type="custom" 
                color="bg-pink-600 hover:bg-pink-700 focus:ring-pink-500" 
                icon="fa-solid fa-heart"
                href="#">
                Favorite
            </x-action-button>
            <x-action-button 
                type="custom" 
                color="bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500" 
                icon="fa-solid fa-bell"
                href="#">
                Notifications
            </x-action-button>
            <x-action-button 
                type="custom" 
                color="bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500" 
                icon="fa-solid fa-star">
                Premium
            </x-action-button>
        </div>
    </div>

    <!-- Real World Examples -->
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Real World Examples</h2>
        
        <!-- Customer Card Example -->
        <div class="border border-gray-200 rounded-lg p-4 mb-4">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">John Doe</h3>
                    <p class="text-sm text-gray-500">john.doe@example.com</p>
                </div>
            </div>
            <div class="flex space-x-2">
                <x-action-button type="view" href="#" />
                <x-action-button type="edit" href="#" />
                <x-action-button type="delete" form-action="#" />
            </div>
        </div>

        <!-- Form Actions Example -->
        <div class="border border-gray-200 rounded-lg p-4">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Edit Customer Form</h3>
            <form class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="John Doe">
                </div>
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                    <x-action-button type="cancel" href="#" />
                    <x-action-button type="save" />
                </div>
            </form>
        </div>
    </div>

    <!-- Code Examples -->
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Code Examples</h2>
        
        <div class="space-y-4">
            <!-- Example 1 -->
            <div>
                <p class="text-sm font-medium text-gray-700 mb-2">Link Button:</p>
                <pre class="bg-gray-50 p-3 rounded text-xs overflow-x-auto"><code>&lt;x-action-button type="edit" :href="route('customers.edit', $customer)" /&gt;</code></pre>
            </div>

            <!-- Example 2 -->
            <div>
                <p class="text-sm font-medium text-gray-700 mb-2">Delete Button with Form:</p>
                <pre class="bg-gray-50 p-3 rounded text-xs overflow-x-auto"><code>&lt;x-action-button type="delete" :form-action="route('customers.destroy', $customer)" /&gt;</code></pre>
            </div>

            <!-- Example 3 -->
            <div>
                <p class="text-sm font-medium text-gray-700 mb-2">Regular Button:</p>
                <pre class="bg-gray-50 p-3 rounded text-xs overflow-x-auto"><code>&lt;x-action-button type="save" /&gt;</code></pre>
            </div>

            <!-- Example 4 -->
            <div>
                <p class="text-sm font-medium text-gray-700 mb-2">Custom Label:</p>
                <pre class="bg-gray-50 p-3 rounded text-xs overflow-x-auto"><code>&lt;x-action-button type="edit" :href="route('customers.edit', $customer)"&gt;
    Update Customer
&lt;/x-action-button&gt;</code></pre>
            </div>

            <!-- Example 5 -->
            <div>
                <p class="text-sm font-medium text-gray-700 mb-2">Different Sizes:</p>
                <pre class="bg-gray-50 p-3 rounded text-xs overflow-x-auto"><code>&lt;x-action-button type="edit" size="sm" :href="route('customers.edit', $customer)" /&gt;
&lt;x-action-button type="edit" size="md" :href="route('customers.edit', $customer)" /&gt;
&lt;x-action-button type="edit" size="lg" :href="route('customers.edit', $customer)" /&gt;</code></pre>
            </div>

            <!-- Example 6 -->
            <div>
                <p class="text-sm font-medium text-gray-700 mb-2">Icon Only:</p>
                <pre class="bg-gray-50 p-3 rounded text-xs overflow-x-auto"><code>&lt;x-action-button type="edit" :href="route('customers.edit', $customer)" :icon-only="true" /&gt;</code></pre>
            </div>

            <!-- Example 7 -->
            <div>
                <p class="text-sm font-medium text-gray-700 mb-2">Custom Button:</p>
                <pre class="bg-gray-50 p-3 rounded text-xs overflow-x-auto"><code>&lt;x-action-button 
    type="custom" 
    color="bg-pink-600 hover:bg-pink-700 focus:ring-pink-500" 
    icon="fa-solid fa-heart"
    href="#"&gt;
    Favorite
&lt;/x-action-button&gt;</code></pre>
            </div>
        </div>
    </div>
</div>
@endsection

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Action Button Cheat Sheet</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @media print {
            body { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body class="bg-gray-50 p-8">
    <div class="max-w-6xl mx-auto bg-white shadow-lg rounded-lg p-8">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Action Button Cheat Sheet</h1>
            <p class="text-gray-600">Quick reference for standardized button components</p>
        </div>

        <!-- Quick Usage -->
        <div class="mb-8 bg-blue-50 border-l-4 border-blue-500 p-4">
            <h2 class="text-lg font-semibold text-blue-900 mb-2">Quick Usage</h2>
            <div class="space-y-1 text-sm font-mono text-blue-800">
                <div>&lt;x-action-button type="<span class="text-red-600">edit</span>" :href="<span class="text-green-600">route('...')</span>" /&gt;</div>
                <div>&lt;x-action-button type="<span class="text-red-600">delete</span>" :form-action="<span class="text-green-600">route('...')</span>" /&gt;</div>
                <div>&lt;x-action-button type="<span class="text-red-600">save</span>" /&gt;</div>
            </div>
        </div>

        <!-- Button Grid -->
        <div class="grid grid-cols-4 gap-4 mb-8">
            <!-- Create -->
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="inline-flex items-center h-10 px-4 bg-emerald-600 text-white rounded-md text-sm font-medium mb-2 w-full justify-center">
                    <i class="fa-solid fa-plus mr-2"></i>Create
                </div>
                <div class="text-xs text-gray-600 space-y-1">
                    <div><strong>Type:</strong> create</div>
                    <div><strong>Color:</strong> Emerald</div>
                    <div><strong>Icon:</strong> fa-plus</div>
                </div>
            </div>

            <!-- Edit -->
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="inline-flex items-center h-10 px-4 bg-amber-600 text-white rounded-md text-sm font-medium mb-2 w-full justify-center">
                    <i class="fa-solid fa-pen mr-2"></i>Edit
                </div>
                <div class="text-xs text-gray-600 space-y-1">
                    <div><strong>Type:</strong> edit</div>
                    <div><strong>Color:</strong> Amber</div>
                    <div><strong>Icon:</strong> fa-pen</div>
                </div>
            </div>

            <!-- Delete -->
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="inline-flex items-center h-10 px-4 bg-rose-600 text-white rounded-md text-sm font-medium mb-2 w-full justify-center">
                    <i class="fa-solid fa-trash mr-2"></i>Delete
                </div>
                <div class="text-xs text-gray-600 space-y-1">
                    <div><strong>Type:</strong> delete</div>
                    <div><strong>Color:</strong> Rose</div>
                    <div><strong>Icon:</strong> fa-trash</div>
                </div>
            </div>

            <!-- View -->
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="inline-flex items-center h-10 px-4 bg-sky-600 text-white rounded-md text-sm font-medium mb-2 w-full justify-center">
                    <i class="fa-regular fa-eye mr-2"></i>View
                </div>
                <div class="text-xs text-gray-600 space-y-1">
                    <div><strong>Type:</strong> view</div>
                    <div><strong>Color:</strong> Sky</div>
                    <div><strong>Icon:</strong> fa-eye</div>
                </div>
            </div>

            <!-- Save -->
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="inline-flex items-center h-10 px-4 bg-blue-600 text-white rounded-md text-sm font-medium mb-2 w-full justify-center">
                    <i class="fa-solid fa-floppy-disk mr-2"></i>Save
                </div>
                <div class="text-xs text-gray-600 space-y-1">
                    <div><strong>Type:</strong> save</div>
                    <div><strong>Color:</strong> Blue</div>
                    <div><strong>Icon:</strong> fa-floppy-disk</div>
                </div>
            </div>

            <!-- Cancel -->
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="inline-flex items-center h-10 px-4 bg-gray-600 text-white rounded-md text-sm font-medium mb-2 w-full justify-center">
                    <i class="fa-solid fa-xmark mr-2"></i>Cancel
                </div>
                <div class="text-xs text-gray-600 space-y-1">
                    <div><strong>Type:</strong> cancel</div>
                    <div><strong>Color:</strong> Gray</div>
                    <div><strong>Icon:</strong> fa-xmark</div>
                </div>
            </div>

            <!-- Print -->
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="inline-flex items-center h-10 px-4 bg-purple-600 text-white rounded-md text-sm font-medium mb-2 w-full justify-center">
                    <i class="fa-solid fa-print mr-2"></i>Print
                </div>
                <div class="text-xs text-gray-600 space-y-1">
                    <div><strong>Type:</strong> print</div>
                    <div><strong>Color:</strong> Purple</div>
                    <div><strong>Icon:</strong> fa-print</div>
                </div>
            </div>

            <!-- Export -->
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="inline-flex items-center h-10 px-4 bg-teal-600 text-white rounded-md text-sm font-medium mb-2 w-full justify-center">
                    <i class="fa-solid fa-file-export mr-2"></i>Export
                </div>
                <div class="text-xs text-gray-600 space-y-1">
                    <div><strong>Type:</strong> export</div>
                    <div><strong>Color:</strong> Teal</div>
                    <div><strong>Icon:</strong> fa-file-export</div>
                </div>
            </div>

            <!-- More buttons... -->
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="inline-flex items-center h-10 px-4 bg-green-600 text-white rounded-md text-sm font-medium mb-2 w-full justify-center">
                    <i class="fa-solid fa-check mr-2"></i>Approve
                </div>
                <div class="text-xs text-gray-600 space-y-1">
                    <div><strong>Type:</strong> approve</div>
                    <div><strong>Color:</strong> Green</div>
                    <div><strong>Icon:</strong> fa-check</div>
                </div>
            </div>

            <div class="border border-gray-200 rounded-lg p-4">
                <div class="inline-flex items-center h-10 px-4 bg-red-600 text-white rounded-md text-sm font-medium mb-2 w-full justify-center">
                    <i class="fa-solid fa-ban mr-2"></i>Reject
                </div>
                <div class="text-xs text-gray-600 space-y-1">
                    <div><strong>Type:</strong> reject</div>
                    <div><strong>Color:</strong> Red</div>
                    <div><strong>Icon:</strong> fa-ban</div>
                </div>
            </div>

            <div class="border border-gray-200 rounded-lg p-4">
                <div class="inline-flex items-center h-10 px-4 bg-green-600 text-white rounded-md text-sm font-medium mb-2 w-full justify-center">
                    <i class="fa-solid fa-download mr-2"></i>Download
                </div>
                <div class="text-xs text-gray-600 space-y-1">
                    <div><strong>Type:</strong> download</div>
                    <div><strong>Color:</strong> Green</div>
                    <div><strong>Icon:</strong> fa-download</div>
                </div>
            </div>

            <div class="border border-gray-200 rounded-lg p-4">
                <div class="inline-flex items-center h-10 px-4 bg-cyan-600 text-white rounded-md text-sm font-medium mb-2 w-full justify-center">
                    <i class="fa-solid fa-envelope mr-2"></i>Send
                </div>
                <div class="text-xs text-gray-600 space-y-1">
                    <div><strong>Type:</strong> send</div>
                    <div><strong>Color:</strong> Cyan</div>
                    <div><strong>Icon:</strong> fa-envelope</div>
                </div>
            </div>
        </div>

        <!-- Common Options -->
        <div class="grid grid-cols-2 gap-6 mb-8">
            <div class="border border-gray-200 rounded-lg p-4">
                <h3 class="font-semibold text-gray-900 mb-3">Common Options</h3>
                <div class="space-y-2 text-sm">
                    <div><code class="bg-gray-100 px-2 py-1 rounded">size="sm|md|lg"</code> - Button size</div>
                    <div><code class="bg-gray-100 px-2 py-1 rounded">:icon-only="true"</code> - Icon only</div>
                    <div><code class="bg-gray-100 px-2 py-1 rounded">:confirm="false"</code> - Disable confirm</div>
                    <div><code class="bg-gray-100 px-2 py-1 rounded">label="Text"</code> - Custom label</div>
                </div>
            </div>

            <div class="border border-gray-200 rounded-lg p-4">
                <h3 class="font-semibold text-gray-900 mb-3">Button Types</h3>
                <div class="space-y-2 text-sm">
                    <div><code class="bg-gray-100 px-2 py-1 rounded">:href="..."</code> - Link button</div>
                    <div><code class="bg-gray-100 px-2 py-1 rounded">:form-action="..."</code> - Form button</div>
                    <div><code class="bg-gray-100 px-2 py-1 rounded">type="button"</code> - Regular button</div>
                    <div><code class="bg-gray-100 px-2 py-1 rounded">type="submit"</code> - Submit button</div>
                </div>
            </div>
        </div>

        <!-- Examples -->
        <div class="bg-gray-50 rounded-lg p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Common Patterns</h3>
            <div class="space-y-4">
                <!-- CRUD Actions -->
                <div>
                    <p class="text-sm font-medium text-gray-700 mb-2">CRUD Actions:</p>
                    <pre class="bg-white p-3 rounded border border-gray-200 text-xs overflow-x-auto"><code>&lt;x-action-button type="view" :href="route('items.show', $item)" /&gt;
&lt;x-action-button type="edit" :href="route('items.edit', $item)" /&gt;
&lt;x-action-button type="delete" :form-action="route('items.destroy', $item)" /&gt;</code></pre>
                </div>

                <!-- Form Actions -->
                <div>
                    <p class="text-sm font-medium text-gray-700 mb-2">Form Actions:</p>
                    <pre class="bg-white p-3 rounded border border-gray-200 text-xs overflow-x-auto"><code>&lt;x-action-button type="cancel" :href="route('items.index')" /&gt;
&lt;x-action-button type="save" /&gt;</code></pre>
                </div>

                <!-- Custom Label -->
                <div>
                    <p class="text-sm font-medium text-gray-700 mb-2">Custom Label:</p>
                    <pre class="bg-white p-3 rounded border border-gray-200 text-xs overflow-x-auto"><code>&lt;x-action-button type="create" :href="route('items.create')"&gt;
    Add New Item
&lt;/x-action-button&gt;</code></pre>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 pt-6 border-t border-gray-200 text-center text-sm text-gray-600">
            <p>For full documentation see: <code class="bg-gray-100 px-2 py-1 rounded">/docs/button-system.md</code></p>
        </div>
    </div>

    <div class="max-w-6xl mx-auto mt-4 text-center">
        <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
            <i class="fas fa-print mr-2"></i>
            Print Cheat Sheet
        </button>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alpine.js Test</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-4">Alpine.js Test</h1>
        
        <!-- Simple Alpine.js test -->
        <div x-data="{ message: 'Hello from Alpine!', count: 0 }">
            <p x-text="message" class="text-lg text-blue-600 mb-4"></p>
            
            <div class="mb-4">
                <button @click="count++" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Count: <span x-text="count"></span>
                </button>
            </div>
            
            <!-- Sidebar test -->
            <div x-data="{ sidebarOpen: false }" class="mb-4">
                <button @click="sidebarOpen = !sidebarOpen" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Toggle Sidebar
                </button>
                
                <div class="mt-2 p-4 bg-gray-200 rounded"
                     :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="-translate-x-full"
                     x-transition:enter-end="translate-x-0"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="translate-x-0"
                     x-transition:leave-end="-translate-x-full">
                    <p class="text-sm text-gray-700">This is a test sidebar</p>
                </div>
            </div>
            
            <div class="text-sm text-gray-500">
                <p>If you see this working, Alpine.js is functioning correctly.</p>
            </div>
        </div>
    </div>
</body>
</html>

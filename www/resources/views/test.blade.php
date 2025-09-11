<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Page</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-lg">
            <h1 class="text-2xl font-bold text-gray-900 mb-4">Test Page</h1>
            <div class="space-y-4">
                <div class="p-4 bg-blue-100 rounded">
                    <p class="text-blue-800">This is a test to verify Tailwind CSS is working.</p>
                </div>
                <div class="p-4 bg-green-100 rounded">
                    <p class="text-green-800">If you can see styled elements, the CSS is loading correctly.</p>
                </div>
                <div x-data="{ message: 'Alpine.js is working!' }" class="p-4 bg-yellow-100 rounded">
                    <p class="text-yellow-800" x-text="message"></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

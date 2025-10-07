<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Welcome</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen relative overflow-hidden flex flex-col sm:justify-center items-center pt-6 sm:pt-0 {{ $bgClass ?? 'bg-gray-100' }}">
            @if(!empty($bgStyle))
            <div class="absolute inset-0 pointer-events-none bg-cover bg-center bg-no-repeat filter blur-sm scale-105" style="{{ $bgStyle }}"></div>
            <div class="absolute inset-0 pointer-events-none bg-black/60"></div>
            @endif

            @if(!empty($bgStyle))
            <div class="relative z-10 text-2xl font-bold text-white drop-shadow-sm">
                {{ str_replace('_',' ' ,config('app.name', 'Welcome')) }}
            </div>
            @else
            <div class="relative z-10 text-2xl font-bold text-gray-900">
                {{ str_replace('_',' ' ,config('app.name', 'Welcome')) }}
            </div>
            @endif

            <div class="relative z-10 w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
            
            <!-- Footer: developer credit -->
            <div class="relative z-10 mt-8 text-center text-xs text-gray-400">
                Developed by <a href="https://netsiteweaver.com" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:text-blue-800 hover:underline">Netsiteweaver</a>
            </div>
        </div>
    </body>
</html>

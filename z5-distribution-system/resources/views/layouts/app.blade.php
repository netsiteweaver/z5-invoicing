<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') - {{ $companySettings ? $companySettings->company_name : config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50" x-data="{ sidebarOpen: false }">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 shadow-lg transform transition-transform duration-300 ease-in-out lg:translate-x-0"
             :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full">
            
            <!-- Brand Logo -->
            <div class="flex items-center justify-center h-16 px-4 bg-gradient-to-r from-blue-600 to-purple-600">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center mr-3">
                        <span class="text-blue-600 font-bold text-sm">Z5</span>
                    </div>
                    <h1 class="text-xl font-bold text-white">Distribution</h1>
                </div>
            </div>

            <!-- User Panel -->
            <div class="flex items-center px-4 py-3 border-b border-gray-700">
                <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                    <span class="text-white font-medium text-sm">{{ substr(Auth::user()->name, 0, 1) }}</span>
                </div>
                <div class="flex-1">
                    <p class="text-white text-sm font-medium">{{ Auth::user()->name }}</p>
                    <p class="text-gray-400 text-xs">{{ Auth::user()->user_level ?? 'User' }}</p>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="mt-2 px-2">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" 
                   class="group flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fas fa-tachometer-alt mr-3 h-5 w-5"></i>
                    Dashboard
                </a>

                <!-- Customers -->
                <a href="{{ route('customers.index') }}" 
                   class="group flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('customers.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fas fa-users mr-3 h-5 w-5"></i>
                    Customers
                </a>

                <!-- Products -->
                <div x-data="{ productsOpen: {{ request()->routeIs('products.*') || request()->routeIs('product-categories.*') || request()->routeIs('product-brands.*') ? 'true' : 'false' }} }">
                    <button @click="productsOpen = !productsOpen" 
                            class="group flex items-center justify-between w-full px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('products.*') || request()->routeIs('product-categories.*') || request()->routeIs('product-brands.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        <div class="flex items-center">
                            <i class="fas fa-box mr-3 h-5 w-5"></i>
                            Products
                        </div>
                        <i class="fas fa-chevron-down transition-transform duration-200" :class="productsOpen ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="productsOpen" x-transition class="ml-6 mt-1 space-y-1">
                        <a href="{{ route('products.index') }}" 
                           class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('products.*') ? 'bg-blue-500 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            <i class="far fa-circle mr-2 text-xs"></i>
                            All Products
                        </a>
                        <a href="{{ route('product-categories.index') }}" 
                           class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('product-categories.*') ? 'bg-blue-500 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            <i class="far fa-circle mr-2 text-xs"></i>
                            Categories
                        </a>
                        <a href="{{ route('product-brands.index') }}" 
                           class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('product-brands.*') ? 'bg-blue-500 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            <i class="far fa-circle mr-2 text-xs"></i>
                            Brands
                        </a>
                    </div>
                </div>

                <!-- Orders -->
                <a href="{{ route('orders.index') }}" 
                   class="group flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('orders.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fas fa-shopping-cart mr-3 h-5 w-5"></i>
                    Orders
                </a>

                <!-- Sales -->
                <div x-data="{ salesOpen: {{ request()->routeIs('sales.*') ? 'true' : 'false' }} }">
                    <button @click="salesOpen = !salesOpen" 
                            class="group flex items-center justify-between w-full px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('sales.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        <div class="flex items-center">
                            <i class="fas fa-chart-line mr-3 h-5 w-5"></i>
                            Sales
                        </div>
                        <i class="fas fa-chevron-down transition-transform duration-200" :class="salesOpen ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="salesOpen" x-transition class="ml-6 mt-1 space-y-1">
                        <a href="{{ route('sales.index') }}" 
                           class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('sales.index') ? 'bg-blue-500 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            <i class="far fa-circle mr-2 text-xs"></i>
                            All Sales
                        </a>
                        <a href="{{ route('sales.create') }}" 
                           class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('sales.create') ? 'bg-blue-500 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            <i class="far fa-circle mr-2 text-xs"></i>
                            Create Sale
                        </a>
                    </div>
                </div>

                <!-- Invoices -->
                <div x-data="{ invoicesOpen: false }">
                    <button @click="invoicesOpen = !invoicesOpen" 
                            class="group flex items-center justify-between w-full px-3 py-2 text-sm font-medium rounded-md text-gray-300 hover:bg-gray-700 hover:text-white">
                        <div class="flex items-center">
                            <i class="fas fa-file-invoice mr-3 h-5 w-5"></i>
                            Invoices
                        </div>
                        <i class="fas fa-chevron-down transition-transform duration-200" :class="invoicesOpen ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="invoicesOpen" x-transition class="ml-6 mt-1 space-y-1">
                        <a href="#" class="block px-3 py-2 text-sm rounded-md text-gray-400 hover:bg-gray-700 hover:text-white">
                            <i class="far fa-circle mr-2 text-xs"></i>
                            Create Invoice
                        </a>
                        <a href="#" class="block px-3 py-2 text-sm rounded-md text-gray-400 hover:bg-gray-700 hover:text-white">
                            <i class="far fa-circle mr-2 text-xs"></i>
                            Invoice List
                        </a>
                    </div>
                </div>

                <!-- Inventory -->
                <div x-data="{ inventoryOpen: {{ request()->routeIs('inventory.*') ? 'true' : 'false' }} }">
                    <button @click="inventoryOpen = !inventoryOpen" 
                            class="group flex items-center justify-between w-full px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('inventory.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        <div class="flex items-center">
                            <i class="fas fa-warehouse mr-3 h-5 w-5"></i>
                            Inventory
                        </div>
                        <i class="fas fa-chevron-down transition-transform duration-200" :class="inventoryOpen ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="inventoryOpen" x-transition class="ml-6 mt-1 space-y-1">
                        <a href="{{ route('inventory.index') }}" 
                           class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('inventory.index') ? 'bg-blue-500 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            <i class="far fa-circle mr-2 text-xs"></i>
                            All Inventory
                        </a>
                        <a href="{{ route('inventory.low-stock') }}" 
                           class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('inventory.low-stock') ? 'bg-blue-500 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            <i class="far fa-circle mr-2 text-xs"></i>
                            Low Stock Alert
                        </a>
                        <a href="{{ route('inventory.stock-report') }}" 
                           class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('inventory.stock-report') ? 'bg-blue-500 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            <i class="far fa-circle mr-2 text-xs"></i>
                            Stock Report
                        </a>
                    </div>
                </div>

                <!-- User Management -->
                <div x-data="{ userManagementOpen: {{ request()->routeIs('user-management.*') ? 'true' : 'false' }} }">
                    <button @click="userManagementOpen = !userManagementOpen" 
                            class="group flex items-center justify-between w-full px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('user-management.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        <div class="flex items-center">
                            <i class="fas fa-users mr-3 h-5 w-5"></i>
                            User Management
                        </div>
                        <i class="fas fa-chevron-down transition-transform duration-200" :class="userManagementOpen ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="userManagementOpen" x-transition class="ml-6 mt-1 space-y-1">
                        <a href="{{ route('user-management.index') }}" 
                           class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('user-management.index') ? 'bg-blue-500 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            <i class="far fa-circle mr-2 text-xs"></i>
                            All Users
                        </a>
                        <a href="{{ route('user-management.create') }}" 
                           class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('user-management.create') ? 'bg-blue-500 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            <i class="far fa-circle mr-2 text-xs"></i>
                            Add User
                        </a>
                        <a href="{{ route('user-management.roles') }}" 
                           class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('user-management.roles') ? 'bg-blue-500 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            <i class="far fa-circle mr-2 text-xs"></i>
                            Manage Roles
                        </a>
                        <a href="{{ route('user-management.permissions') }}" 
                           class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('user-management.permissions') ? 'bg-blue-500 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            <i class="far fa-circle mr-2 text-xs"></i>
                            Manage Permissions
                        </a>
                    </div>
                </div>

                <!-- Divider -->
                <div class="border-t border-gray-700 my-4"></div>
                <div class="px-3 py-2">
                    <p class="text-gray-500 text-xs font-semibold uppercase tracking-wider">System</p>
                </div>

                <!-- Settings -->
                <a href="{{ route('settings.index') }}" 
                   class="group flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('settings.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fas fa-cogs mr-3 h-5 w-5"></i>
                    Settings
                </a>
            </nav>
        </div>

        <!-- Main content -->
        <div class="flex-1 flex flex-col lg:ml-64">
            <!-- Top navigation -->
            <div class="bg-white shadow-sm border-b border-gray-200">
                <div class="flex justify-between items-center h-16 px-4">
                    <!-- Mobile menu button -->
                    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': sidebarOpen, 'inline-flex': ! sidebarOpen }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': ! sidebarOpen, 'inline-flex': sidebarOpen }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <!-- Breadcrumbs -->
                    <div class="flex-1">
                        <nav class="flex" aria-label="Breadcrumb">
                            <ol class="flex items-center space-x-4">
                                <li>
                                    <div>
                                        <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-500">
                                            <i class="fas fa-home"></i>
                                            <span class="sr-only">Home</span>
                                        </a>
                                    </div>
                                </li>
                                <li>
                                    <div class="flex items-center">
                                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                                        <span class="text-sm font-medium text-gray-500">@yield('title', 'Dashboard')</span>
                                    </div>
                                </li>
                            </ol>
                        </nav>
                    </div>

                    <!-- Right side -->
                    <div class="flex items-center space-x-4">
                        <!-- Notifications -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="p-2 text-gray-400 hover:text-gray-500 relative">
                                <i class="far fa-bell text-lg"></i>
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">3</span>
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg py-1 z-50 border">
                                <div class="px-4 py-2 border-b">
                                    <h3 class="text-sm font-medium text-gray-900">Notifications</h3>
                                </div>
                                <a href="#" class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-100">
                                    <div class="flex items-center">
                                        <i class="fas fa-envelope text-blue-500 mr-3"></i>
                                        <div>
                                            <p class="font-medium">New order received</p>
                                            <p class="text-gray-500 text-xs">Order #ORD-12345</p>
                                        </div>
                                    </div>
                                </a>
                                <a href="#" class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-100">
                                    <div class="flex items-center">
                                        <i class="fas fa-exclamation-triangle text-yellow-500 mr-3"></i>
                                        <div>
                                            <p class="font-medium">Low stock alert</p>
                                            <p class="text-gray-500 text-xs">Product ABC-123</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <!-- User menu -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <div class="h-8 w-8 bg-blue-500 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-medium text-white">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                </div>
                                <span class="ml-2 text-gray-700 hidden md:block">{{ Auth::user()->name }}</span>
                                <i class="fas fa-chevron-down ml-1 text-gray-400"></i>
                            </button>

                            <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border">
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user mr-2"></i> Profile
                                </a>
                                <a href="{{ route('settings.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-cog mr-2"></i> Settings
                                </a>
                                <div class="border-t border-gray-100"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page Header -->
            <div class="bg-white border-b border-gray-200">
                <div class="px-4 py-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">@yield('title', 'Dashboard')</h1>
                            <!-- <p class="mt-1 text-sm text-gray-500">@yield('description', 'Welcome to your dashboard')</p> -->
                        </div>
                        <div class="flex space-x-3">
                            @yield('actions')
                        </div>
                    </div>
                </div>
            </div>

            <!-- Breadcrumb -->
            @if(isset($breadcrumbs) && count($breadcrumbs) > 1)
            <div class="bg-gray-50 border-b border-gray-200">
                <div class="px-4 py-3">
                    <nav class="flex" aria-label="Breadcrumb">
                        <ol class="flex items-center space-x-2">
                            @foreach($breadcrumbs as $breadcrumb)
                            <li>
                                <div class="flex items-center">
                                    @if(!$loop->first)
                                    <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    @endif
                                    
                                    @if($breadcrumb['current'])
                                        <span class="{{ $loop->first ? 'text-gray-400' : 'ml-2 text-sm font-medium text-gray-500' }}" aria-current="page">
                                            @if($loop->first)
                                                <svg class="flex-shrink-0 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                                                </svg>
                                                <span class="sr-only">{{ $breadcrumb['title'] }}</span>
                                            @else
                                                {{ $breadcrumb['title'] }}
                                            @endif
                                        </span>
                                    @else
                                        <a href="{{ $breadcrumb['url'] }}" class="{{ $loop->first ? 'text-gray-400 hover:text-gray-500' : 'ml-2 text-sm font-medium text-gray-500 hover:text-gray-700' }}">
                                            @if($loop->first)
                                                <svg class="flex-shrink-0 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                                                </svg>
                                                <span class="sr-only">{{ $breadcrumb['title'] }}</span>
                                            @else
                                                {{ $breadcrumb['title'] }}
                                            @endif
                                        </a>
                                    @endif
                                </div>
                            </li>
                            @endforeach
                        </ol>
                    </nav>
                </div>
            </div>
            @endif

            <!-- Page content -->
            <main class="flex-1 overflow-y-auto bg-gray-50">
                <div class="py-6">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        @yield('content')
                    </div>
                </div>
            </main>
        </div>

        <!-- Mobile sidebar overlay -->
        <div x-show="sidebarOpen"
             x-cloak
             @click="sidebarOpen = false"
             class="fixed inset-0 z-40 bg-gray-600 bg-opacity-75 lg:hidden"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
        </div>
    </div>
</body>
</html>
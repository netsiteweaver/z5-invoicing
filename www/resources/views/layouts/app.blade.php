<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') - {{ $companySettings ? $companySettings->company_name : ($displayAppName ?? config('app.name', 'Welcome')) }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Favicons -->
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <link rel="mask-icon" href="{{ asset('safari-pinned-tab.svg') }}" color="#0ea5e9">
    <meta name="msapplication-TileImage" content="{{ asset('mstile-150x150.png') }}">
    <meta name="msapplication-TileColor" content="#0ea5e9">
    <meta name="theme-color" content="#0ea5e9">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50" x-data="{ sidebarOpen: false }" x-init="sidebarOpen = window.matchMedia('(min-width: 1024px)').matches">
    <div class="min-h-screen flex transition-all duration-300 app-container" :style="{ paddingLeft: sidebarOpen ? '16rem' : '0' }">
        <!-- Sidebar -->
        <div class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 shadow-lg transform transition-transform duration-300 ease-in-out flex flex-col print:hidden no-print sidebar h-screen"
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
                    <div class="w-8 h-8 bg-white rounded flex items-center justify-center mr-3 overflow-hidden">
                        <img src="{{ asset('favicon-32x32.png') }}" alt="Logo" class="h-8 w-8 object-contain">
                    </div>
                    <h1 class="text-lg sm:text-xl font-bold text-white truncate">{{ $companySettings ? $companySettings->company_name : ($displayAppName ?? config('app.name', 'Welcome')) }}</h1>
                </div>
            </div>

            <!-- User Panel (clickable to profile) -->
            <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-3 border-b border-gray-700 hover:bg-gray-800">
                <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                    <span class="text-white font-medium text-sm">{{ substr(Auth::user()->name, 0, 1) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-white text-sm font-medium truncate">{{ Auth::user()->name }}</p>
                    <p class="text-gray-400 text-xs truncate">{{ Auth::user()->user_level ?? 'User' }}</p>
                </div>
            </a>

            <!-- Navigation -->
            <nav class="px-2" @click="if (window.innerWidth < 1024 && $event.target.closest('a')) sidebarOpen = false">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" 
                   class="group flex items-center px-3 py-3 text-sm font-medium rounded-md {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fas fa-tachometer-alt mr-3 h-5 w-5 flex-shrink-0"></i>
                    <span class="truncate">Dashboard</span>
                </a>

                <!-- Customers -->
                @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('customers.view'))
                <div x-data="{ customersOpen: {{ request()->routeIs('customers.*') ? 'true' : 'false' }} }">
                    <button @click="customersOpen = !customersOpen" 
                            class="group flex items-center justify-between w-full px-3 py-3 text-sm font-medium rounded-md {{ request()->routeIs('customers.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        <div class="flex items-center flex-1 min-w-0">
                            <i class="fas fa-users mr-3 h-5 w-5 flex-shrink-0"></i>
                            <span class="truncate">Customers</span>
                        </div>
                        <i class="fas fa-chevron-down transition-transform duration-200 flex-shrink-0" :class="customersOpen ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="customersOpen" x-transition class="ml-6 mt-1 space-y-1">
                        @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('customers.view'))
                        <a href="{{ route('customers.index') }}" 
                           class="flex items-center px-3 py-3 text-sm rounded-md {{ request()->routeIs('customers.index') ? 'bg-blue-500 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            <i class="far fa-circle mr-3 text-xs flex-shrink-0"></i>
                            <span class="truncate">All Customers</span>
                        </a>
                        @endif
                        @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('customers.create'))
                        <a href="{{ route('customers.create') }}" 
                           class="flex items-center px-3 py-3 text-sm rounded-md {{ request()->routeIs('customers.create') ? 'bg-blue-500 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            <i class="far fa-circle mr-3 text-xs flex-shrink-0"></i>
                            <span class="truncate">Create Customer</span>
                        </a>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Products -->
                @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('products.view') || auth()->user()->hasPermission('product_categories.view') || auth()->user()->hasPermission('product_brands.view'))
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
                        @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('products.view'))
                        <a href="{{ route('products.index') }}" 
                           class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('products.*') ? 'bg-blue-500 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            <i class="far fa-circle mr-2 text-xs"></i>
                            All Products
                        </a>
                        @endif
                        @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('product_categories.view'))
                        <a href="{{ route('product-categories.index') }}" 
                           class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('product-categories.*') ? 'bg-blue-500 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            <i class="far fa-circle mr-2 text-xs"></i>
                            Categories
                        </a>
                        @endif
                        @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('product_brands.view'))
                        <a href="{{ route('product-brands.index') }}" 
                           class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('product-brands.*') ? 'bg-blue-500 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            <i class="far fa-circle mr-2 text-xs"></i>
                            Brands
                        </a>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Orders -->
                @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('orders.view'))
                <div x-data="{ ordersOpen: {{ request()->routeIs('orders.*') ? 'true' : 'false' }} }">
                    <button @click="ordersOpen = !ordersOpen" 
                            class="group flex items-center justify-between w-full px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('orders.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        <div class="flex items-center">
                            <i class="fas fa-shopping-cart mr-3 h-5 w-5"></i>
                            Orders
                        </div>
                        <i class="fas fa-chevron-down transition-transform duration-200" :class="ordersOpen ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="ordersOpen" x-transition class="ml-6 mt-1 space-y-1">
                        @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('orders.view'))
                        <a href="{{ route('orders.index') }}" 
                           class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('orders.index') ? 'bg-blue-500 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            <i class="far fa-circle mr-2 text-xs"></i>
                            All Orders
                        </a>
                        @endif
                        @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('orders.create'))
                        <a href="{{ route('orders.create') }}" 
                           class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('orders.create') ? 'bg-blue-500 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            <i class="far fa-circle mr-2 text-xs"></i>
                            Create Order
                        </a>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Sales -->
                @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('sales.view'))
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
                        @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('sales.view'))
                        <a href="{{ route('sales.index') }}" 
                           class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('sales.index') ? 'bg-blue-500 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            <i class="far fa-circle mr-2 text-xs"></i>
                            All Sales
                        </a>
                        @endif
                        @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('sales.create'))
                        <a href="{{ route('sales.create') }}" 
                           class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('sales.create') ? 'bg-blue-500 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            <i class="far fa-circle mr-2 text-xs"></i>
                            Create Sale
                        </a>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Payments -->
                @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('payments.view') || auth()->user()->hasPermission('payments.create'))
                <div x-data="{ paymentsOpen: {{ request()->routeIs('payments.*') ? 'true' : 'false' }} }">
                    <button @click="paymentsOpen = !paymentsOpen" 
                            class="group flex items-center justify-between w-full px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('payments.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        <div class="flex items-center">
                            <i class="fas fa-money-bill-wave mr-3 h-5 w-5"></i>
                            Payments
                        </div>
                        <i class="fas fa-chevron-down transition-transform duration-200" :class="paymentsOpen ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="paymentsOpen" x-transition class="ml-6 mt-1 space-y-1">
                        @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('payments.view'))
                        <a href="{{ route('payments.index') }}" 
                           class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('payments.index') ? 'bg-blue-500 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            <i class="far fa-circle mr-2 text-xs"></i>
                            All Payments
                        </a>
                        @endif
                        @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('payments.create'))
                        <a href="{{ route('payments.create') }}" 
                           class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('payments.create') ? 'bg-blue-500 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            <i class="far fa-circle mr-2 text-xs"></i>
                            Record Payment
                        </a>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Inventory -->
                @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('inventory.view'))
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
                        @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('inventory.view'))
                        <a href="{{ route('inventory.index') }}" 
                           class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('inventory.index') ? 'bg-blue-500 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            <i class="far fa-circle mr-2 text-xs"></i>
                            All Inventory
                        </a>
                        @endif
                        @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('inventory.low_stock'))
                        <a href="{{ route('inventory.low-stock') }}" 
                           class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('inventory.low-stock') ? 'bg-blue-500 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            <i class="far fa-circle mr-2 text-xs"></i>
                            Low Stock Alert
                        </a>
                        @endif
                        @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('inventory.stock_report'))
                        <!-- <a href="{{ route('inventory.stock-report') }}" 
                           class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('inventory.stock-report') ? 'bg-blue-500 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            <i class="far fa-circle mr-2 text-xs"></i>
                            Stock Report
                        </a> -->
                        @endif

                        @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('goods_receipts.view'))
                        <a href="{{ route('goods-receipts.index') }}" 
                           class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('goods-receipts.*') ? 'bg-blue-500 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            <i class="far fa-circle mr-2 text-xs"></i>
                            Goods Receipts
                        </a>
                        @endif

                        @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('stock_transfers.view'))
                        <a href="{{ route('stock-transfers.index') }}" 
                           class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('stock-transfers.*') ? 'bg-blue-500 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            <i class="far fa-circle mr-2 text-xs"></i>
                            Stock Transfers
                        </a>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Payment Terms (CRUD) moved above System block -->
                @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('settings.view'))
                <div x-data="{ termsOpen: {{ request()->routeIs('payment-terms.*') ? 'true' : 'false' }} }">
                    <button @click="termsOpen = !termsOpen" 
                            class="group flex items-center justify-between w-full px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('payment-terms.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        <div class="flex items-center">
                            <i class="fas fa-calendar-alt mr-3 h-5 w-5"></i>
                            Payment Terms
                        </div>
                        <i class="fas fa-chevron-down transition-transform duration-200" :class="termsOpen ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="termsOpen" x-transition class="ml-6 mt-1 space-y-1">
                        <a href="{{ route('payment-terms.index') }}" 
                           class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('payment-terms.index') ? 'bg-blue-500 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            <i class="far fa-circle mr-2 text-xs"></i>
                            All Payment Terms
                        </a>
                        @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('settings.edit'))
                        <a href="{{ route('payment-terms.create') }}" 
                           class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('payment-terms.create') ? 'bg-blue-500 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            <i class="far fa-circle mr-2 text-xs"></i>
                            Create Payment Term
                        </a>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Units of Measure -->
                @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('uoms.view'))
                <div x-data="{ uomsOpen: {{ request()->routeIs('uoms.*') ? 'true' : 'false' }} }">
                    <button @click="uomsOpen = !uomsOpen" 
                            class="group flex items-center justify-between w-full px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('uoms.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        <div class="flex items-center">
                            <i class="fas fa-ruler-combined mr-3 h-5 w-5"></i>
                            Units of Measure
                        </div>
                        <i class="fas fa-chevron-down transition-transform duration-200" :class="uomsOpen ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="uomsOpen" x-transition class="ml-6 mt-1 space-y-1">
                        <a href="{{ route('uoms.index') }}" 
                           class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('uoms.index') ? 'bg-blue-500 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            <i class="far fa-circle mr-2 text-xs"></i>
                            All UOMs
                        </a>
                        @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('uoms.create'))
                        <a href="{{ route('uoms.create') }}" 
                           class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('uoms.create') ? 'bg-blue-500 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            <i class="far fa-circle mr-2 text-xs"></i>
                            Create UOM
                        </a>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Suppliers -->
                @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('suppliers.view'))
                <div x-data="{ suppliersOpen: {{ request()->routeIs('suppliers.*') ? 'true' : 'false' }} }">
                    <button @click="suppliersOpen = !suppliersOpen" 
                            class="group flex items-center justify-between w-full px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('suppliers.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        <div class="flex items-center">
                            <i class="fas fa-truck mr-3 h-5 w-5"></i>
                            Suppliers
                        </div>
                        <i class="fas fa-chevron-down transition-transform duration-200" :class="suppliersOpen ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="suppliersOpen" x-transition class="ml-6 mt-1 space-y-1">
                        @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('suppliers.view'))
                        <a href="{{ route('suppliers.index') }}" 
                           class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('suppliers.index') ? 'bg-blue-500 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            <i class="far fa-circle mr-2 text-xs"></i>
                            All Suppliers
                        </a>
                        @endif
                        @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('suppliers.create'))
                        <a href="{{ route('suppliers.create') }}" 
                           class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('suppliers.create') ? 'bg-blue-500 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            <i class="far fa-circle mr-2 text-xs"></i>
                            Create Supplier
                        </a>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Departments -->
                @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('departments.view'))
                <div x-data="{ departmentsOpen: {{ request()->routeIs('departments.*') ? 'true' : 'false' }} }">
                    <button @click="departmentsOpen = !departmentsOpen" 
                            class="group flex items-center justify-between w-full px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('departments.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        <div class="flex items-center">
                            <i class="fas fa-building mr-3 h-5 w-5"></i>
                            Departments
                        </div>
                        <i class="fas fa-chevron-down transition-transform duration-200" :class="departmentsOpen ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="departmentsOpen" x-transition class="ml-6 mt-1 space-y-1">
                        @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('departments.view'))
                        <a href="{{ route('departments.index') }}" 
                           class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('departments.index') ? 'bg-blue-500 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            <i class="far fa-circle mr-2 text-xs"></i>
                            All Departments
                        </a>
                        @endif
                        @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('departments.create'))
                        <a href="{{ route('departments.create') }}" 
                           class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('departments.create') ? 'bg-blue-500 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            <i class="far fa-circle mr-2 text-xs"></i>
                            Create Department
                        </a>
                        @endif
                    </div>
                </div>
                @endif

                <!-- User Management -->
                @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('user_management.view'))
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
                        @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('user_management.view'))
                        <a href="{{ route('user-management.index') }}" 
                           class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('user-management.index') ? 'bg-blue-500 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            <i class="far fa-circle mr-2 text-xs"></i>
                            All Users
                        </a>
                        @endif
                        @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('user_management.create'))
                        <a href="{{ route('user-management.create') }}" 
                           class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('user-management.create') ? 'bg-blue-500 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            <i class="far fa-circle mr-2 text-xs"></i>
                            Add User
                        </a>
                        @endif
                        @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('user_management.roles'))
                        <a href="{{ route('user-management.roles') }}" 
                           class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('user-management.roles') ? 'bg-blue-500 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            <i class="far fa-circle mr-2 text-xs"></i>
                            Manage Roles
                        </a>
                        @endif
                        @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('user_management.permissions'))
                        <a href="{{ route('user-management.permissions') }}" 
                           class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('user-management.permissions') ? 'bg-blue-500 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            <i class="far fa-circle mr-2 text-xs"></i>
                            Manage Permissions
                        </a>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Divider -->
                <div class="border-t border-gray-700 my-4"></div>
                <div class="px-3 py-2">
                    <p class="text-gray-500 text-xs font-semibold uppercase tracking-wider">System</p>
                </div>

                <!-- Reports -->
                @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('reports.view'))
                <div x-data="{ reportsOpen: {{ request()->routeIs('reports.*') ? 'true' : 'false' }} }">
                    <button @click="reportsOpen = !reportsOpen" 
                            class="group flex items-center justify-between w-full px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('reports.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        <div class="flex items-center">
                            <i class="fas fa-chart-bar mr-3 h-5 w-5"></i>
                            Reports
                        </div>
                        <i class="fas fa-chevron-down transition-transform duration-200" :class="reportsOpen ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="reportsOpen" x-transition class="ml-6 mt-1 space-y-1">
                        <a href="{{ route('reports.index') }}" 
                           class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('reports.index') ? 'bg-blue-500 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            <i class="far fa-circle mr-2 text-xs"></i>
                            All Reports
                        </a>
                        @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('reports.orders'))
                        <a href="{{ route('reports.orders') }}" 
                           class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('reports.orders') ? 'bg-blue-500 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            <i class="far fa-circle mr-2 text-xs"></i>
                            Orders Report
                        </a>
                        @endif
                        @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('reports.sales'))
                        <a href="{{ route('reports.sales') }}" 
                           class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('reports.sales') ? 'bg-blue-500 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            <i class="far fa-circle mr-2 text-xs"></i>
                            Sales Report
                        </a>
                        @endif
                        @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('reports.goods_receipts'))
                        <a href="{{ route('reports.goods-receipts') }}" 
                           class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('reports.goods-receipts') ? 'bg-blue-500 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            <i class="far fa-circle mr-2 text-xs"></i>
                            Goods Receipts
                        </a>
                        @endif
                        @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('reports.payments'))
                        <a href="{{ route('reports.payments') }}" 
                           class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('reports.payments') ? 'bg-blue-500 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            <i class="far fa-circle mr-2 text-xs"></i>
                            Payment Analysis
                        </a>
                        @endif
                        @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('reports.stock_transfers'))
                        <a href="{{ route('reports.stock-transfers') }}" 
                           class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('reports.stock-transfers') ? 'bg-blue-500 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            <i class="far fa-circle mr-2 text-xs"></i>
                            Stock Transfers
                        </a>
                        @endif
                        @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('reports.inventory'))
                        <a href="{{ route('reports.inventory') }}" 
                           class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('reports.inventory') ? 'bg-blue-500 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            <i class="far fa-circle mr-2 text-xs"></i>
                            Inventory Report
                        </a>
                        @endif
                        @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('reports.customers'))
                        <a href="{{ route('reports.customers') }}" 
                           class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('reports.customers') ? 'bg-blue-500 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            <i class="far fa-circle mr-2 text-xs"></i>
                            Customer Analysis
                        </a>
                        @endif
                        @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('reports.suppliers'))
                        <a href="{{ route('reports.suppliers') }}" 
                           class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('reports.suppliers') ? 'bg-blue-500 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            <i class="far fa-circle mr-2 text-xs"></i>
                            Supplier Analysis
                        </a>
                        @endif
                        @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('reports.monthly_summary'))
                        <a href="{{ route('reports.monthly-summary') }}" 
                           class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('reports.monthly-summary') ? 'bg-blue-500 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            <i class="far fa-circle mr-2 text-xs"></i>
                            Monthly Summary
                        </a>
                        @endif
                        @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('reports.growth_analysis'))
                        <a href="{{ route('reports.growth-analysis') }}" 
                           class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('reports.growth-analysis') ? 'bg-blue-500 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            <i class="far fa-circle mr-2 text-xs"></i>
                            Growth Analysis
                        </a>
                        @endif
                        @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('reports.alerts_warnings'))
                        <a href="{{ route('reports.alerts-warnings') }}" 
                           class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('reports.alerts-warnings') ? 'bg-blue-500 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            <i class="far fa-circle mr-2 text-xs"></i>
                            Alerts & Warnings
                        </a>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Settings -->
                @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('settings.view'))
                <div x-data="{ settingsOpen: {{ request()->routeIs('settings.*') || request()->routeIs('settings.notifications') || request()->routeIs('payment-types.*') ? 'true' : 'false' }} }">
                    <button @click="settingsOpen = !settingsOpen" 
                            class="group flex items-center justify-between w-full px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('settings.*') || request()->routeIs('settings.notifications') || request()->routeIs('payment-types.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        <div class="flex items-center">
                            <i class="fas fa-cogs mr-3 h-5 w-5"></i>
                            Settings
                        </div>
                        <i class="fas fa-chevron-down transition-transform duration-200" :class="settingsOpen ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="settingsOpen" x-transition class="ml-6 mt-1 space-y-1">
                        <a href="{{ route('settings.index') }}" 
                           class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('settings.index') ? 'bg-blue-500 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            <i class="far fa-circle mr-2 text-xs"></i>
                            Company Settings
                        </a>
                        <a href="{{ route('settings.notifications') }}" 
                           class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('settings.notifications') ? 'bg-blue-500 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            <i class="far fa-circle mr-2 text-xs"></i>
                            Notifications
                        </a>
                        @if(auth()->user()->is_admin || auth()->user()->is_root || auth()->user()->hasPermission('payment-types.view'))
                        <a href="{{ route('payment-types.index') }}" 
                           class="block px-3 py-2 text-sm rounded-md {{ request()->routeIs('payment-types.*') ? 'bg-blue-500 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                            <i class="far fa-circle mr-2 text-xs"></i>
                            Payment Types
                        </a>
                        @endif
                    </div>
                </div>
                @endif
                </nav>

        </div>

        <!-- Main content -->
        <div class="flex-1 flex flex-col">
            <!-- Top navigation -->
            <div class="bg-white shadow-sm border-b border-gray-200 print:hidden no-print">
                <div class="flex justify-between items-center h-16 px-4">
                    <!-- Mobile menu button -->
                    <button @click="sidebarOpen = !sidebarOpen" title="Toggle sidebar" aria-label="Toggle sidebar" class="inline-flex items-center justify-center p-3 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 touch-manipulation">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': sidebarOpen, 'inline-flex': ! sidebarOpen }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': ! sidebarOpen, 'inline-flex': sidebarOpen }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <!-- Breadcrumbs -->
                    <div class="flex-1 min-w-0">
                        <nav class="flex" aria-label="Breadcrumb">
                            <ol class="flex items-center space-x-2 sm:space-x-4 overflow-hidden">
                                <li class="flex-shrink-0">
                                    <div>
                                        <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-500 p-1 rounded touch-manipulation">
                                            <i class="fas fa-home"></i>
                                            <span class="sr-only">Home</span>
                                        </a>
                                    </div>
                                </li>
                                @hasSection('breadcrumbs')
                                @yield('breadcrumbs')
                                @endif
                                <li class="flex-shrink-0">
                                    <div class="flex items-center">
                                        <i class="fas fa-chevron-right text-gray-400 mx-1 sm:mx-2 text-xs"></i>
                                        <span class="text-sm font-medium text-gray-500 truncate">@yield('title', 'Dashboard')</span>
                                    </div>
                                </li>
                            </ol>
                        </nav>
                    </div>

                    <!-- Right side -->
                    <div class="flex items-center space-x-2 sm:space-x-4">
                        <!-- Low Stock Notification -->
                        @if($lowStockCount > 0)
                        <div class="relative" x-data="{ open: false }">
                            <a href="{{ route('inventory.low-stock') }}" 
                               class="p-2 text-gray-400 hover:text-gray-500 relative group touch-manipulation"
                               title="{{ $lowStockCount }} {{ Str::plural('item', $lowStockCount) }} running low on stock">
                                <i class="fas fa-exclamation-triangle text-lg text-yellow-500"></i>
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-medium">{{ $lowStockCount }}</span>
                            </a>
                        </div>
                        @endif

                        <!-- User menu -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 p-1 touch-manipulation">
                                <div class="h-8 w-8 bg-blue-500 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-medium text-white">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                </div>
                                <span class="ml-2 text-gray-700 hidden sm:block truncate max-w-24">{{ Auth::user()->name }}</span>
                                <i class="fas fa-chevron-down ml-1 text-gray-400 hidden sm:block"></i>
                            </button>

                            <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border">
                                <a href="{{ route('manual') }}" target="_blank" rel="noopener" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 touch-manipulation">
                                    <i class="fas fa-book mr-3 flex-shrink-0"></i> 
                                    <span>User Manual</span>
                                </a>
                                <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 touch-manipulation">
                                    <i class="fas fa-user mr-3 flex-shrink-0"></i> 
                                    <span>Profile</span>
                                </a>
                                <a href="{{ route('settings.index') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 touch-manipulation">
                                    <i class="fas fa-cog mr-3 flex-shrink-0"></i> 
                                    <span>Settings</span>
                                </a>
                                <div class="border-t border-gray-100"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 touch-manipulation">
                                        <i class="fas fa-sign-out-alt mr-3 flex-shrink-0"></i> 
                                        <span>Logout</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page Header -->
            <div class="bg-white border-b border-gray-200 print:hidden no-print">
                <div class="px-4 py-4">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-3 sm:space-y-0">
                        <div class="min-w-0 flex-1">
                            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 truncate">@yield('title', 'Dashboard')</h1>
                            @hasSection('description')
                            <p class="mt-1 text-sm text-gray-500">@yield('description')</p>
                            @endif
                        </div>
                        @hasSection('actions')
                        <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3 no-print print:hidden">
                            @yield('actions')
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            

            <!-- Page content -->
            <main class="flex-1 overflow-y-auto bg-gray-50">
                @if(request()->routeIs('reports.*'))
                <div class="px-4 py-4 hidden print:block">
                    <div class="max-w-7xl mx-auto">
                        <h1 class="text-2xl font-bold text-gray-900">@yield('title', 'Report')</h1>
                        <p class="text-sm text-gray-700">Printed on {{ now()->format('M d, Y H:i') }}</p>
                    </div>
                </div>
                @endif
                <div class="py-6">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        @if(session('success'))
                        <div class="mb-4 rounded-md bg-green-50 p-4 no-print print:hidden">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-check-circle text-green-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if(session('error'))
                        <div class="mb-4 rounded-md bg-red-50 p-4 no-print print:hidden">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-triangle text-red-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if ($errors->any())
                        <div class="mb-4 rounded-md bg-red-50 p-4 no-print print:hidden">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-times-circle text-red-400"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">There were some problems with your input:</h3>
                                    <div class="mt-2 text-sm text-red-700">
                                        <ul class="list-disc pl-5 space-y-1">
                                            @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        @yield('content')
                        @include('components.release-notes-modal')
                    </div>
                </div>
            </main>

            <!-- Footer: version info -->
            <div class="bg-white border-t border-gray-200">
                <div class="px-4 py-2 text-xs text-gray-500 flex items-center justify-between">
                    <span>{{ $displayAppName ?? config('app.name') }}</span>
                    <span x-data="{v:''}" x-init="(async()=>{try{const r=await fetch('{{ route('changelog.feed') }}',{cache:'no-cache'});if(r.ok){const d=await r.json();v=(d.releases?.[0]?.version)||''}}catch(e){}})()">Version <span x-text="v || '–'"></span></span>
                </div>
            </div>

        </div>

        <!-- Mobile sidebar overlay -->
        <div x-show="sidebarOpen"
             x-cloak
             @click="sidebarOpen = false"
             class="fixed inset-0 z-40 bg-gray-600 bg-opacity-75 lg:hidden print:hidden no-print"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
        </div>
    </div>
@stack('scripts')

<script>
// Prevent form double submission
document.addEventListener('DOMContentLoaded', function() {
    // Handle all forms on the page
    document.querySelectorAll('form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            const submitButton = form.querySelector('button[type="submit"], input[type="submit"]');
            
            if (submitButton && !submitButton.disabled) {
                // Disable the submit button
                submitButton.disabled = true;
                
                // Add loading state
                const originalText = submitButton.textContent || submitButton.value;
                if (submitButton.tagName === 'BUTTON') {
                    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
                } else {
                    submitButton.value = 'Processing...';
                }
                
                // Re-enable after 10 seconds as a safety measure
                setTimeout(function() {
                    submitButton.disabled = false;
                    if (submitButton.tagName === 'BUTTON') {
                        submitButton.innerHTML = originalText;
                    } else {
                        submitButton.value = originalText;
                    }
                }, 10000);
            }
        });
    });
    
    // Handle specific buttons with onclick handlers that submit forms
    document.querySelectorAll('button[onclick*="submit"], input[onclick*="submit"]').forEach(function(button) {
        button.addEventListener('click', function(e) {
            if (!button.disabled) {
                button.disabled = true;
                const originalText = button.textContent || button.value;
                
                if (button.tagName === 'BUTTON') {
                    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
                } else {
                    button.value = 'Processing...';
                }
                
                // Re-enable after 10 seconds
                setTimeout(function() {
                    button.disabled = false;
                    if (button.tagName === 'BUTTON') {
                        button.innerHTML = originalText;
                    } else {
                        button.value = originalText;
                    }
                }, 10000);
            }
        });
    });
});
</script>

</body>
</html>
# Dashboard & Analytics

## Overview
This document defines the dashboard and analytics features for the Z5 Distribution System. It includes key performance indicators, reporting capabilities, and data visualization requirements.

## Dashboard Design Principles

### Core Principles
- **Real-time Data**: Display current system status and metrics
- **Actionable Insights**: Provide data that drives business decisions
- **User-Centric**: Customize views based on user roles and permissions
- **Mobile Responsive**: Optimized for all device types
- **Performance**: Fast loading with efficient data queries

### Dashboard Layout
- **Header**: User info, notifications, quick actions
- **Sidebar**: Navigation menu with role-based access
- **Main Content**: Key metrics, charts, and data tables
- **Footer**: System status, version info, quick links

## Key Performance Indicators (KPIs)

### Order Management KPIs
- **Total Orders**: Count of all orders
- **Pending Orders**: Orders awaiting confirmation
- **Confirmed Orders**: Orders ready for processing
- **Processing Orders**: Orders being prepared
- **Shipped Orders**: Orders in transit
- **Delivered Orders**: Completed orders
- **Cancelled Orders**: Cancelled orders
- **Order Value**: Total monetary value of orders
- **Average Order Value**: Mean order value
- **Order Growth**: Month-over-month growth rate

### Sales Management KPIs
- **Total Sales**: Count of all sales
- **Sales Revenue**: Total monetary value of sales
- **Monthly Sales**: Current month sales
- **Sales Growth**: Month-over-month growth
- **Conversion Rate**: Orders to sales conversion
- **Average Sale Value**: Mean sale value
- **Top Products**: Best-selling products
- **Sales by Status**: Sales distribution by status

### Customer Management KPIs
- **Total Customers**: Count of all customers
- **New Customers**: Customers added this month
- **Active Customers**: Customers with recent activity
- **Customer Growth**: Month-over-month growth
- **Customer Retention**: Repeat customer percentage
- **Average Customer Value**: Mean customer lifetime value
- **Top Customers**: Highest value customers
- **Customer Satisfaction**: Based on feedback and ratings

### Payment Management KPIs
- **Total Payments**: Count of all payments
- **Payment Revenue**: Total payments received
- **Outstanding Payments**: Unpaid amounts
- **Overdue Payments**: Past due payments
- **Payment Methods**: Distribution by payment type
- **Collection Rate**: Percentage of payments collected
- **Average Payment Time**: Days to payment
- **Payment Trends**: Payment patterns over time

### Inventory Management KPIs
- **Total Products**: Count of all products
- **Low Stock Items**: Products below reorder point
- **Out of Stock Items**: Products with zero inventory
- **Inventory Value**: Total inventory worth
- **Stock Turnover**: Inventory rotation rate
- **Reorder Alerts**: Items needing reorder
- **Stock Movements**: Inventory changes
- **Department Stock**: Stock by location

## Dashboard Components

### Summary Cards
```html
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-2 bg-blue-100 rounded-lg">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Orders</p>
                <p class="text-2xl font-semibold text-gray-900">1,234</p>
                <p class="text-sm text-green-600">+12% from last month</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-2 bg-green-100 rounded-lg">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Sales Revenue</p>
                <p class="text-2xl font-semibold text-gray-900">Rs 2,345,678</p>
                <p class="text-sm text-green-600">+8% from last month</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-2 bg-yellow-100 rounded-lg">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Customers</p>
                <p class="text-2xl font-semibold text-gray-900">456</p>
                <p class="text-sm text-green-600">+5% from last month</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-2 bg-red-100 rounded-lg">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Low Stock Items</p>
                <p class="text-2xl font-semibold text-gray-900">23</p>
                <p class="text-sm text-red-600">Needs attention</p>
            </div>
        </div>
    </div>
</div>
```

### Charts and Graphs

#### Sales Trend Chart
```html
<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-medium text-gray-900 mb-4">Sales Trend</h3>
    <div class="h-64">
        <canvas id="salesTrendChart"></canvas>
    </div>
</div>

<script>
const salesTrendCtx = document.getElementById('salesTrendChart').getContext('2d');
const salesTrendChart = new Chart(salesTrendCtx, {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [{
            label: 'Sales Revenue',
            data: [120000, 150000, 180000, 200000, 220000, 250000],
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'Rs ' + value.toLocaleString();
                    }
                }
            }
        }
    }
});
</script>
```

#### Order Status Distribution
```html
<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-medium text-gray-900 mb-4">Order Status Distribution</h3>
    <div class="h-64">
        <canvas id="orderStatusChart"></canvas>
    </div>
</div>

<script>
const orderStatusCtx = document.getElementById('orderStatusChart').getContext('2d');
const orderStatusChart = new Chart(orderStatusCtx, {
    type: 'doughnut',
    data: {
        labels: ['Pending', 'Confirmed', 'Processing', 'Shipped', 'Delivered', 'Cancelled'],
        datasets: [{
            data: [25, 50, 30, 20, 120, 5],
            backgroundColor: [
                'rgb(251, 191, 36)',
                'rgb(59, 130, 246)',
                'rgb(139, 92, 246)',
                'rgb(16, 185, 129)',
                'rgb(34, 197, 94)',
                'rgb(239, 68, 68)'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>
```

#### Top Products Chart
```html
<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-medium text-gray-900 mb-4">Top Products</h3>
    <div class="h-64">
        <canvas id="topProductsChart"></canvas>
    </div>
</div>

<script>
const topProductsCtx = document.getElementById('topProductsChart').getContext('2d');
const topProductsChart = new Chart(topProductsCtx, {
    type: 'bar',
    data: {
        labels: ['Product A', 'Product B', 'Product C', 'Product D', 'Product E'],
        datasets: [{
            label: 'Quantity Sold',
            data: [150, 120, 100, 80, 60],
            backgroundColor: 'rgb(59, 130, 246)'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>
```

### Data Tables

#### Recent Orders Table
```html
<div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Recent Orders</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Order Number
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Customer
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Date
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Total
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        ORD2024010001
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        ABC Company Ltd
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        2024-01-15
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            Confirmed
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        Rs 1,250.00
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
```

#### Low Stock Alerts Table
```html
<div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Low Stock Alerts</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Product
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Current Stock
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Reorder Point
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Action
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        Laptop Computer
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        5
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        20
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                            Low Stock
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <button class="text-blue-600 hover:text-blue-800">Reorder</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
```

## Reporting Features

### Sales Reports
- **Daily Sales Report**: Sales by day with trends
- **Monthly Sales Report**: Monthly sales summary
- **Product Sales Report**: Sales by product
- **Customer Sales Report**: Sales by customer
- **Salesperson Report**: Sales by user
- **Payment Report**: Payment status and trends

### Inventory Reports
- **Stock Level Report**: Current inventory levels
- **Low Stock Report**: Items below reorder point
- **Stock Movement Report**: Inventory changes
- **Department Stock Report**: Stock by location
- **Product Performance Report**: Product sales vs inventory

### Customer Reports
- **Customer List Report**: All customers with details
- **Customer Activity Report**: Customer order history
- **Customer Value Report**: Customer lifetime value
- **New Customer Report**: Recently added customers
- **Customer Satisfaction Report**: Feedback and ratings

### Financial Reports
- **Revenue Report**: Total revenue by period
- **Payment Report**: Payment status and trends
- **Outstanding Payments Report**: Unpaid amounts
- **Profit Margin Report**: Profit analysis
- **Cost Analysis Report**: Cost breakdown

## Real-time Updates

### WebSocket Integration
```javascript
// Real-time dashboard updates
const socket = new WebSocket('ws://localhost:6001');

socket.onmessage = function(event) {
    const data = JSON.parse(event.data);
    
    switch(data.type) {
        case 'order.created':
            updateOrderCount(data.count);
            break;
        case 'sale.completed':
            updateSalesRevenue(data.revenue);
            break;
        case 'payment.received':
            updatePaymentStatus(data.payment);
            break;
        case 'stock.updated':
            updateStockLevels(data.stock);
            break;
    }
};
```

### Auto-refresh Components
```html
<div x-data="dashboardData()" x-init="startAutoRefresh()">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Orders</p>
                    <p class="text-2xl font-semibold text-gray-900" x-text="orders.total"></p>
                    <p class="text-sm text-green-600" x-text="orders.growth"></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function dashboardData() {
    return {
        orders: {
            total: 0,
            growth: '+0%'
        },
        sales: {
            revenue: 0,
            growth: '+0%'
        },
        customers: {
            total: 0,
            growth: '+0%'
        },
        inventory: {
            lowStock: 0
        },
        
        startAutoRefresh() {
            this.fetchData();
            setInterval(() => {
                this.fetchData();
            }, 30000); // Refresh every 30 seconds
        },
        
        async fetchData() {
            try {
                const response = await fetch('/api/dashboard/summary');
                const data = await response.json();
                
                this.orders = data.orders;
                this.sales = data.sales;
                this.customers = data.customers;
                this.inventory = data.inventory;
            } catch (error) {
                console.error('Failed to fetch dashboard data:', error);
            }
        }
    }
}
</script>
```

## Mobile Dashboard

### Responsive Design
```html
<div class="min-h-screen bg-gray-100">
    <!-- Mobile Header -->
    <div class="lg:hidden bg-white shadow-sm">
        <div class="px-4 py-3">
            <h1 class="text-xl font-semibold text-gray-900">Dashboard</h1>
        </div>
    </div>
    
    <!-- Desktop Header -->
    <div class="hidden lg:block bg-white shadow-sm">
        <div class="px-6 py-4">
            <h1 class="text-2xl font-semibold text-gray-900">Dashboard</h1>
        </div>
    </div>
    
    <!-- Mobile Cards -->
    <div class="lg:hidden p-4 space-y-4">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Orders</p>
                    <p class="text-2xl font-semibold text-gray-900">1,234</p>
                </div>
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Desktop Grid -->
    <div class="hidden lg:block p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Desktop cards -->
        </div>
    </div>
</div>
```

## Export and Sharing

### Export Options
- **PDF Export**: Generate PDF reports
- **Excel Export**: Export data to Excel
- **CSV Export**: Export data to CSV
- **Email Reports**: Send reports via email
- **Scheduled Reports**: Automated report generation

### Sharing Features
- **Dashboard Sharing**: Share dashboard views
- **Report Sharing**: Share specific reports
- **Custom Views**: Create custom dashboard views
- **User Preferences**: Save user preferences
- **Bookmarks**: Bookmark important views

This comprehensive dashboard and analytics system provides real-time insights, comprehensive reporting, and mobile-responsive design for the Z5 Distribution System.

# Development Guidelines

## Overview
This document establishes coding standards, best practices, and development guidelines for the Z5 Distribution System. These guidelines ensure code quality, maintainability, and consistency across the development team.

## Coding Standards

### PHP/Laravel Standards

#### PSR-12 Compliance
- Follow PSR-12 coding standard
- Use 4 spaces for indentation (no tabs)
- Maximum line length of 120 characters
- Use meaningful variable and method names
- Follow camelCase for methods and variables
- Use PascalCase for class names

#### Laravel Conventions
```php
// Model naming
class Order extends Model
class OrderItem extends Model

// Controller naming
class OrderController extends Controller
class OrderItemController extends Controller

// Method naming
public function index()
public function store(Request $request)
public function show($id)
public function update(Request $request, $id)
public function destroy($id)
```

#### Database Conventions
```php
// Migration naming
create_orders_table
create_order_items_table
add_status_to_orders_table

// Column naming
created_at
updated_at
deleted_at
user_id
order_id
```

### Type Declarations
```php
// Always use type hints
public function createOrder(array $data): Order
public function calculateTotal(float $subtotal, float $tax): float
public function isValidEmail(string $email): bool

// Use return types
public function getOrders(): Collection
public function findOrder(int $id): ?Order
public function processPayment(Payment $payment): bool
```

### Error Handling
```php
// Use try-catch blocks for exceptions
try {
    $order = Order::create($data);
    return response()->json(['success' => true, 'data' => $order]);
} catch (Exception $e) {
    Log::error('Order creation failed', ['error' => $e->getMessage()]);
    return response()->json(['success' => false, 'message' => 'Order creation failed'], 500);
}

// Use custom exceptions
throw new OrderNotFoundException('Order not found');
throw new InsufficientStockException('Insufficient stock available');
```

## Architecture Patterns

### MVC Pattern
- **Models**: Handle data logic and database interactions
- **Views**: Handle presentation logic (Blade templates)
- **Controllers**: Handle business logic and request/response

### Repository Pattern
```php
interface OrderRepositoryInterface
{
    public function findById(int $id): ?Order;
    public function findByUuid(string $uuid): ?Order;
    public function create(array $data): Order;
    public function update(Order $order, array $data): Order;
    public function delete(Order $order): bool;
}

class OrderRepository implements OrderRepositoryInterface
{
    public function findById(int $id): ?Order
    {
        return Order::find($id);
    }
    
    public function create(array $data): Order
    {
        return Order::create($data);
    }
}
```

### Service Pattern
```php
class OrderService
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private InventoryService $inventoryService,
        private EmailService $emailService
    ) {}
    
    public function createOrder(array $data): Order
    {
        DB::beginTransaction();
        
        try {
            $order = $this->orderRepository->create($data);
            $this->inventoryService->reserveStock($order->items);
            $this->emailService->sendOrderConfirmation($order);
            
            DB::commit();
            return $order;
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
```

## Database Guidelines

### Migration Best Practices
```php
// Use descriptive migration names
Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->uuid('uuid')->unique();
    $table->string('order_number')->unique();
    $table->foreignId('customer_id')->constrained()->onDelete('cascade');
    $table->date('order_date');
    $table->enum('status', ['draft', 'pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled']);
    $table->decimal('total_amount', 10, 2);
    $table->timestamps();
    $table->softDeletes();
    
    $table->index(['customer_id', 'status']);
    $table->index('order_date');
});

// Use rollback methods
Schema::dropIfExists('orders');
```

### Model Relationships
```php
class Order extends Model
{
    protected $fillable = [
        'customer_id', 'order_date', 'status', 'total_amount'
    ];
    
    protected $casts = [
        'order_date' => 'date',
        'total_amount' => 'decimal:2'
    ];
    
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
    
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
    
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
```

### Query Optimization
```php
// Use eager loading to prevent N+1 queries
$orders = Order::with(['customer', 'items.product'])->get();

// Use select to limit columns
$orders = Order::select(['id', 'order_number', 'total_amount'])->get();

// Use where clauses efficiently
$orders = Order::where('status', 'confirmed')
    ->where('order_date', '>=', now()->subDays(30))
    ->get();
```

## API Development

### RESTful API Design
```php
// Use proper HTTP methods
Route::get('/orders', [OrderController::class, 'index']);
Route::post('/orders', [OrderController::class, 'store']);
Route::get('/orders/{order}', [OrderController::class, 'show']);
Route::put('/orders/{order}', [OrderController::class, 'update']);
Route::delete('/orders/{order}', [OrderController::class, 'destroy']);

// Use resource routes
Route::apiResource('orders', OrderController::class);
Route::apiResource('orders.items', OrderItemController::class);
```

### Response Format
```php
// Success response
return response()->json([
    'success' => true,
    'message' => 'Order created successfully',
    'data' => $order,
    'meta' => [
        'timestamp' => now()->toISOString(),
        'version' => '1.0'
    ]
], 201);

// Error response
return response()->json([
    'success' => false,
    'message' => 'Validation failed',
    'errors' => $validator->errors(),
    'meta' => [
        'timestamp' => now()->toISOString(),
        'version' => '1.0'
    ]
], 422);
```

### Validation
```php
// Use Form Request classes
class CreateOrderRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'order_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0'
        ];
    }
    
    public function messages(): array
    {
        return [
            'customer_id.required' => 'Customer is required',
            'items.required' => 'At least one item is required'
        ];
    }
}
```

## Frontend Development

### Blade Templates
```blade
{{-- Use consistent indentation --}}
@extends('layouts.app')

@section('title', 'Orders')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Orders</h1>
        <a href="{{ route('orders.create') }}" 
           class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            Create Order
        </a>
    </div>
    
    <div class="bg-white rounded-lg shadow">
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
                        Total
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($orders as $order)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $order->order_number }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $order->customer->company_name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ number_format($order->total_amount, 2) }} MUR
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
```

### Alpine.js Components
```html
<div x-data="orderForm()" class="space-y-6">
    <div>
        <label class="block text-sm font-medium text-gray-700">Customer</label>
        <select x-model="form.customer_id" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            <option value="">Select Customer</option>
            <template x-for="customer in customers" :key="customer.id">
                <option :value="customer.id" x-text="customer.company_name"></option>
            </template>
        </select>
    </div>
    
    <div class="space-y-4">
        <template x-for="(item, index) in form.items" :key="index">
            <div class="flex space-x-4">
                <select x-model="item.product_id" 
                        class="flex-1 rounded-md border-gray-300 shadow-sm">
                    <option value="">Select Product</option>
                    <template x-for="product in products" :key="product.id">
                        <option :value="product.id" x-text="product.name"></option>
                    </template>
                </select>
                <input type="number" x-model="item.quantity" 
                       class="w-20 rounded-md border-gray-300 shadow-sm">
                <input type="number" x-model="item.unit_price" 
                       class="w-24 rounded-md border-gray-300 shadow-sm">
                <button type="button" @click="removeItem(index)"
                        class="text-red-600 hover:text-red-800">Remove</button>
            </div>
        </template>
    </div>
    
    <button @click="addItem()" 
            class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
        Add Item
    </button>
</div>

<script>
function orderForm() {
    return {
        form: {
            customer_id: '',
            items: []
        },
        customers: @json($customers),
        products: @json($products),
        
        addItem() {
            this.form.items.push({
                product_id: '',
                quantity: 1,
                unit_price: 0
            });
        },
        
        removeItem(index) {
            this.form.items.splice(index, 1);
        }
    }
}
</script>
```

### TailwindCSS Guidelines
```css
/* Use consistent spacing */
.container { @apply mx-auto px-4; }
.section { @apply py-8; }
.card { @apply bg-white rounded-lg shadow; }

/* Use consistent colors */
.primary { @apply bg-blue-600 text-white; }
.secondary { @apply bg-gray-600 text-white; }
.success { @apply bg-green-600 text-white; }
.danger { @apply bg-red-600 text-white; }

/* Use consistent typography */
.heading-1 { @apply text-3xl font-bold text-gray-900; }
.heading-2 { @apply text-2xl font-semibold text-gray-900; }
.body-text { @apply text-gray-700; }
```

## Testing Guidelines

### Unit Tests
```php
class OrderTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_can_create_order()
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();
        
        $orderData = [
            'customer_id' => $customer->id,
            'order_date' => now()->toDateString(),
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                    'unit_price' => 100.00
                ]
            ]
        ];
        
        $response = $this->postJson('/api/orders', $orderData);
        
        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'order_number',
                    'customer_id',
                    'total_amount'
                ]
            ]);
        
        $this->assertDatabaseHas('orders', [
            'customer_id' => $customer->id,
            'total_amount' => 200.00
        ]);
    }
}
```

### Feature Tests
```php
class OrderManagementTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_user_can_view_orders()
    {
        $user = User::factory()->create();
        $orders = Order::factory()->count(5)->create();
        
        $response = $this->actingAs($user)
            ->get('/orders');
        
        $response->assertStatus(200)
            ->assertViewIs('orders.index')
            ->assertViewHas('orders');
    }
    
    public function test_user_can_create_order()
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();
        
        $response = $this->actingAs($user)
            ->post('/orders', [
                'customer_id' => $customer->id,
                'order_date' => now()->toDateString(),
                'items' => [
                    [
                        'product_id' => $product->id,
                        'quantity' => 1,
                        'unit_price' => 100.00
                    ]
                ]
            ]);
        
        $response->assertRedirect('/orders');
        $this->assertDatabaseHas('orders', [
            'customer_id' => $customer->id
        ]);
    }
}
```

## Security Guidelines

### Input Validation
```php
// Always validate input
$request->validate([
    'email' => 'required|email|max:255',
    'password' => 'required|min:8|confirmed',
    'phone' => 'required|regex:/^[\+]?[0-9\s\-\(\)]+$/'
]);

// Sanitize input
$cleanInput = filter_var($input, FILTER_SANITIZE_STRING);
```

### Authentication
```php
// Use middleware for authentication
Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('orders', OrderController::class);
});

// Check permissions
if (!auth()->user()->can('create', Order::class)) {
    abort(403, 'Unauthorized action');
}
```

### Data Protection
```php
// Use mass assignment protection
protected $fillable = [
    'name', 'email', 'phone'
];

protected $guarded = [
    'id', 'created_at', 'updated_at'
];

// Hide sensitive data
protected $hidden = [
    'password', 'remember_token'
];
```

## Performance Guidelines

### Database Optimization
```php
// Use eager loading
$orders = Order::with(['customer', 'items.product'])->get();

// Use select to limit columns
$orders = Order::select(['id', 'order_number', 'total_amount'])->get();

// Use pagination
$orders = Order::paginate(15);

// Use database transactions
DB::transaction(function () {
    $order = Order::create($data);
    $this->updateInventory($order);
    $this->sendNotification($order);
});
```

### Caching
```php
// Cache expensive queries
$orders = Cache::remember('orders.recent', 3600, function () {
    return Order::where('created_at', '>=', now()->subDays(7))->get();
});

// Cache configuration
$config = Cache::remember('app.config', 86400, function () {
    return Config::all();
});
```

### Frontend Optimization
```html
<!-- Use lazy loading for images -->
<img src="image.jpg" loading="lazy" alt="Product image">

<!-- Use CDN for assets -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">

<!-- Minify CSS and JS -->
<link rel="stylesheet" href="{{ asset('css/app.min.css') }}">
<script src="{{ asset('js/app.min.js') }}"></script>
```

## Documentation Standards

### Code Documentation
```php
/**
 * Create a new order with items and update inventory
 *
 * @param array $data Order data including customer and items
 * @return Order Created order instance
 * @throws InsufficientStockException When stock is insufficient
 * @throws ValidationException When data validation fails
 */
public function createOrder(array $data): Order
{
    // Implementation
}
```

### API Documentation
```php
/**
 * @OA\Post(
 *     path="/api/orders",
 *     summary="Create a new order",
 *     description="Creates a new order with items and updates inventory",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="customer_id", type="integer", example=1),
 *             @OA\Property(property="order_date", type="string", format="date", example="2024-01-15"),
 *             @OA\Property(
 *                 property="items",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="product_id", type="integer", example=1),
 *                     @OA\Property(property="quantity", type="integer", example=2),
 *                     @OA\Property(property="unit_price", type="number", format="float", example=100.00)
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Order created successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Order created successfully"),
 *             @OA\Property(property="data", ref="#/components/schemas/Order")
 *         )
 *     )
 * )
 */
```

## Deployment Guidelines

### Environment Configuration
```bash
# Use environment variables
APP_ENV=production
APP_DEBUG=false
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=z5_distribution
DB_USERNAME=username
DB_PASSWORD=password
```

### Production Checklist
- [ ] Set APP_ENV=production
- [ ] Set APP_DEBUG=false
- [ ] Configure proper database credentials
- [ ] Set up SSL certificates
- [ ] Configure email settings
- [ ] Set up monitoring and logging
- [ ] Configure backup systems
- [ ] Test all functionality
- [ ] Performance optimization
- [ ] Security hardening

These development guidelines ensure consistent, maintainable, and high-quality code throughout the Z5 Distribution System development process.

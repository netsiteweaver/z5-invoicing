<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Concerns\HasBreadcrumbs;

class OrderController extends Controller
{
    use HasBreadcrumbs;
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->middleware('permission:orders.view')->only(['index', 'show']);
        $this->middleware('permission:orders.create')->only(['create', 'store']);
        $this->middleware('permission:orders.edit')->only(['edit', 'update']);
        $this->middleware('permission:orders.delete')->only(['destroy']);
        $this->middleware('permission:orders.convert_to_sale')->only(['convertToSale']);
    }
    /**
     * Display a listing of orders.
     */
    public function index(Request $request)
    {
        $query = Order::with(['customer', 'items.product', 'createdBy'])->withCount('sales');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($customerQuery) use ($search) {
                      $customerQuery->where('company_name', 'like', "%{$search}%")
                                   ->orWhere('full_name', 'like', "%{$search}%")
                                   ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('order_status', $request->status);
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by customer
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('order_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('order_date', '<=', $request->date_to);
        }

        $orders = $query->active()->latest()->paginate(15);
        $customers = Customer::active()->ordered()->get();

        $breadcrumbs = $this->setBreadcrumbs('orders.index');

        return view('orders.index', compact('orders', 'customers') + $breadcrumbs);
    }

    /**
     * Show the form for creating a new order.
     */
    public function create()
    {
        $customers = Customer::active()->ordered()->get();
        $products = Product::active()->with(['category', 'brand', 'inventory'])->get();
        
        return view('orders.create', compact('customers', 'products'));
    }

    /**
     * Store a newly created order.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'order_date' => ['required', 'date'],
            'delivery_date' => ['nullable', 'date', 'after_or_equal:order_date'],
            'order_status' => ['required', 'in:draft,pending,confirmed,processing,shipped,delivered,cancelled'],
            // payment_status is managed automatically
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        // Calculate totals
        $subtotal = 0;
        $totalDiscount = 0;
        $totalTax = 0;
        
        foreach ($validated['items'] as $item) {
            $lineTotal = $item['quantity'] * $item['unit_price'];
            $discountAmount = $lineTotal * ($item['discount_percent'] ?? 0) / 100;
            $subtotal += $lineTotal;
            $totalDiscount += $discountAmount;
            $product = Product::find($item['product_id']);
            $taxType = $product?->tax_type ?? 'standard';
            $taxPercent = match ($taxType) {
                'standard' => 15,
                'zero' => 0,
                'exempt' => 0,
                default => 0,
            };
            $taxAmount = ($lineTotal - $discountAmount) * ($taxPercent / 100);
            $totalTax += $taxAmount;
        }

        $validated['subtotal'] = $subtotal;
        $validated['discount_amount'] = $totalDiscount;
        $validated['tax_amount'] = $totalTax;
        $validated['total_amount'] = $subtotal - $totalDiscount + $totalTax;
        $validated['created_by'] = auth()->id();
        $validated['status'] = 1;
        $validated['payment_status'] = 'pending';

        if (empty($validated['delivery_date'])) {
            $validated['delivery_date'] = $validated['order_date'];
        }

        $order = Order::create($validated);

        // Create order items
        foreach ($validated['items'] as $item) {
            $lineTotal = $item['quantity'] * $item['unit_price'];
            $discountAmount = $lineTotal * ($item['discount_percent'] ?? 0) / 100;
            $product = Product::find($item['product_id']);
            $taxType = $product?->tax_type ?? 'standard';
            $taxPercent = match ($taxType) {
                'standard' => 15,
                'zero' => 0,
                'exempt' => 0,
                default => 0,
            };
            $taxAmount = ($lineTotal - $discountAmount) * ($taxPercent / 100);
            
            $order->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'discount_percent' => $item['discount_percent'] ?? 0,
                'discount_amount' => $discountAmount,
                'tax_type' => $taxType,
                'tax_percent' => $taxPercent,
                'tax_amount' => $taxAmount,
                'line_total' => $lineTotal - $discountAmount + $taxAmount,
                'created_by' => auth()->id(),
                'status' => 1,
            ]);
        }

        return redirect()->route('orders.show', $order)
            ->with('success', 'Order created successfully.');
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        $order->load(['customer', 'items.product', 'createdBy', 'updatedBy'])->loadCount('sales');
        
        $breadcrumbs = $this->setBreadcrumbs('orders.show', ['order' => $order]);
        
        return view('orders.show', compact('order') + $breadcrumbs);
    }

    /**
     * Show the form for editing the specified order.
     */
    public function edit(Order $order)
    {
        if (!$order->canBeEdited()) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'This order cannot be edited in its current status.');
        }

        // Ensure product relation is available for VAT type defaults in the view
        $order->load(['items.product']);

        $customers = Customer::active()->ordered()->get();
        $products = Product::active()->with(['category', 'brand', 'inventory'])->get();
        
        return view('orders.edit', compact('order', 'customers', 'products'));
    }

    /**
     * Update the specified order.
     */
    public function update(Request $request, Order $order)
    {
        if (!$order->canBeEdited()) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'This order cannot be edited in its current status.');
        }

        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'order_date' => ['required', 'date'],
            'delivery_date' => ['nullable', 'date', 'after_or_equal:order_date'],
            'order_status' => ['required', 'in:draft,pending,confirmed,processing,shipped,delivered,cancelled'],
            // payment_status is managed automatically
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        // Calculate totals
        $subtotal = 0;
        $totalDiscount = 0;
        $totalTax = 0;
        
        foreach ($validated['items'] as $item) {
            $lineTotal = $item['quantity'] * $item['unit_price'];
            $discountAmount = $lineTotal * ($item['discount_percent'] ?? 0) / 100;
            $subtotal += $lineTotal;
            $totalDiscount += $discountAmount;
            $product = Product::find($item['product_id']);
            $taxType = $product?->tax_type ?? 'standard';
            $taxPercent = match ($taxType) {
                'standard' => 15,
                'zero' => 0,
                'exempt' => 0,
                default => 0,
            };
            $taxAmount = ($lineTotal - $discountAmount) * ($taxPercent / 100);
            $totalTax += $taxAmount;
        }

        $validated['subtotal'] = $subtotal;
        $validated['discount_amount'] = $totalDiscount;
        $validated['tax_amount'] = $totalTax;
        $validated['total_amount'] = $subtotal - $totalDiscount + $totalTax;
        $validated['updated_by'] = auth()->id();
        // Do not accept payment_status from request
        unset($validated['payment_status']);

        $order->update($validated);

        // Update order items (delete existing and create new)
        $order->items()->delete();
        foreach ($validated['items'] as $item) {
            $lineTotal = $item['quantity'] * $item['unit_price'];
            $discountAmount = $lineTotal * ($item['discount_percent'] ?? 0) / 100;
            $product = Product::find($item['product_id']);
            $taxType = $product?->tax_type ?? 'standard';
            $taxPercent = match ($taxType) {
                'standard' => 15,
                'zero' => 0,
                'exempt' => 0,
                default => 0,
            };
            $taxAmount = ($lineTotal - $discountAmount) * ($taxPercent / 100);
            
            $order->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'discount_percent' => $item['discount_percent'] ?? 0,
                'discount_amount' => $discountAmount,
                'tax_type' => $taxType,
                'tax_percent' => $taxPercent,
                'tax_amount' => $taxAmount,
                'line_total' => $lineTotal - $discountAmount + $taxAmount,
                'created_by' => auth()->id(),
                'status' => 1,
            ]);
        }

        return redirect()->route('orders.show', $order)
            ->with('success', 'Order updated successfully.');
    }

    /**
     * Remove the specified order from storage.
     */
    public function destroy(Order $order)
    {
        if (!$order->canBeEdited()) {
            return redirect()->route('orders.index')
                ->with('error', 'This order cannot be deleted in its current status.');
        }

        $order->update(['status' => 0]);

        return redirect()->route('orders.index')
            ->with('success', 'Order deleted successfully.');
    }

    /**
     * Convert order to sale.
     */
    public function convertToSale(Order $order)
    {
        if ($order->order_status !== 'confirmed') {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Only confirmed orders can be converted to sales.');
        }

        // Ensure items are loaded
        $order->load(['items']);

        DB::beginTransaction();
        try {
            // Prevent double conversion
            if (\App\Models\Sale::where('order_id', $order->id)->exists()) {
                DB::rollBack();
                return redirect()->route('orders.show', $order)
                    ->with('error', 'This order has already been converted to a sale.');
            }
            // Create sale from order
            $sale = \App\Models\Sale::create([
                'order_id' => $order->id,
                'customer_id' => $order->customer_id,
                'sale_date' => now(),
                'delivery_date' => $order->delivery_date,
                'subtotal' => $order->subtotal,
                'tax_amount' => $order->tax_amount,
                'discount_amount' => $order->discount_amount,
                'total_amount' => $order->total_amount,
                'notes' => "Converted from Order #{$order->order_number}",
                'sale_status' => 'confirmed',
                'payment_status' => 'pending',
                'created_by' => auth()->id(),
            ]);

            // Create sale items from order items
            foreach ($order->items as $orderItem) {
                \App\Models\SaleItem::create([
                    'sale_id' => $sale->id,
                    'order_item_id' => $orderItem->id,
                    'product_id' => $orderItem->product_id,
                    'quantity' => $orderItem->quantity,
                    'unit_price' => $orderItem->unit_price,
                    'discount_percent' => $orderItem->discount_percent ?? 0,
                    'discount_amount' => $orderItem->discount_amount ?? 0,
                    'tax_percent' => $orderItem->tax_percent ?? 0,
                    'tax_amount' => $orderItem->tax_amount ?? 0,
                    'line_total' => $orderItem->line_total,
                    'status' => 1,
                ]);

                // Update inventory
                $this->updateInventory($orderItem->product_id, $orderItem->quantity, 'out', $sale->id, 'sale_from_order');
            }

            // Keep status as confirmed (DB enum doesn't include 'converted')
            $order->update(['order_status' => 'confirmed']);

            DB::commit();

            return redirect()->route('sales.show', $sale)
                ->with('success', 'Order converted to sale successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->route('orders.show', $order)
                ->with('error', 'Failed to convert order to sale: ' . $e->getMessage());
        }
    }

    private function updateInventory($productId, $quantity, $movementType, $referenceId, $notes)
    {
        // Find inventory for the product (assuming main department for now)
        $inventory = \App\Models\Inventory::where('product_id', $productId)->first();
        
        if ($inventory) {
            if ($movementType === 'out') {
                if ($inventory->current_stock < $quantity) {
                    throw new \Exception("Insufficient stock for product. Available: {$inventory->current_stock}");
                }
                $inventory->decrement('current_stock', $quantity);
            } else {
                $inventory->increment('current_stock', $quantity);
            }

            // Create stock movement
            \App\Models\StockMovement::create([
                'product_id' => $inventory->product_id,
                'department_id' => $inventory->department_id,
                'movement_type' => $movementType,
                'quantity' => $quantity,
                'reference_type' => 'sale',
                'reference_id' => $referenceId,
                'notes' => $notes,
                'created_by' => auth()->id(),
            ]);
        }
    }
}

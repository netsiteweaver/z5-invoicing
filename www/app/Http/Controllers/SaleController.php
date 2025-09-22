<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\StockMovement;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Http\Controllers\Concerns\HasBreadcrumbs;

class SaleController extends Controller
{
    use HasBreadcrumbs;
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->middleware('permission:sales.view')->only(['index', 'show']);
        $this->middleware('permission:sales.create')->only(['create', 'store', 'convertFromOrder']);
        $this->middleware('permission:sales.edit')->only(['edit', 'update']);
        $this->middleware('permission:sales.delete')->only(['destroy']);
    }
    public function index(Request $request)
    {
        $query = Sale::with(['customer', 'items.product']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('sale_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($customerQuery) use ($search) {
                      $customerQuery->where('company_name', 'like', "%{$search}%")
                                   ->orWhere('full_name', 'like', "%{$search}%")
                                   ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by customer
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('sale_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('sale_date', '<=', $request->date_to);
        }

        $sales = $query->orderBy('created_at', 'desc')->paginate(20);
        $customers = Customer::ordered()->get();

        $breadcrumbs = $this->setBreadcrumbs('sales.index');

        return view('sales.index', compact('sales', 'customers') + $breadcrumbs);
    }

    public function show(Sale $sale)
    {
        $sale->load(['customer', 'items.product', 'payments']);
        $paymentTypes = \App\Models\PaymentType::orderBy('display_order')->orderBy('name')->get();
        
        $breadcrumbs = $this->setBreadcrumbs('sales.show', ['sale' => $sale]);
        
        return view('sales.show', compact('sale', 'paymentTypes') + $breadcrumbs);
    }

    public function create()
    {
        $customers = Customer::ordered()->get();
        $products = Product::ordered()->get();
        $paymentTerms = \App\Models\PaymentTerm::where('status', 1)->orderBy('is_default', 'desc')->orderBy('name')->get();
        return view('sales.create', compact('customers', 'products', 'paymentTerms'));
    }

    public function store(Request $request, InventoryService $inventoryService)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'sale_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:sale_date',
            'payment_term_id' => 'nullable|exists:payment_terms,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.uom_id' => 'nullable|exists:uoms,id',
            'items.*.uom_quantity' => 'nullable|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_percentage' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            // Calculate totals
            $subtotal = 0;
            $totalDiscount = 0;
            
            foreach ($request->items as $item) {
                $itemTotal = $item['quantity'] * $item['unit_price'];
                $discountAmount = $itemTotal * ($item['discount_percentage'] ?? 0) / 100;
                $subtotal += $itemTotal;
                $totalDiscount += $discountAmount;
            }

            $totalAmount = $subtotal - $totalDiscount;

            // Determine due date from payment terms if provided and not manual
            $dueDate = $request->due_date;
            if ($request->filled('payment_term_id')) {
                $term = \App\Models\PaymentTerm::find($request->payment_term_id);
                if ($term) {
                    if ($term->type === 'days') {
                        $dueDate = \Carbon\Carbon::parse($request->sale_date)->addDays($term->days)->toDateString();
                    } elseif ($term->type === 'eom') {
                        $dueDate = \Carbon\Carbon::parse($request->sale_date)->endOfMonth()->toDateString();
                    } // manual keeps provided due_date
                }
            }

            // Create sale
            $sale = Sale::create([
                'customer_id' => $request->customer_id,
                'sale_date' => $request->sale_date,
                'due_date' => $dueDate,
                'subtotal' => $subtotal,
                'discount_amount' => $totalDiscount,
                'total_amount' => $totalAmount,
                'notes' => $request->notes,
                'status' => 'confirmed',
                'payment_status' => 'pending',
                'payment_term_id' => $request->payment_term_id,
                'created_by' => auth()->id(),
            ]);

            // Create sale items
            foreach ($request->items as $item) {
                $itemTotal = $item['quantity'] * $item['unit_price'];
                $discountAmount = $itemTotal * ($item['discount_percentage'] ?? 0) / 100;
                
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'uom_id' => $item['uom_id'] ?? null,
                    'uom_quantity' => $item['uom_quantity'] ?? 1,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount_percentage' => $item['discount_percentage'] ?? 0,
                    'discount_amount' => $discountAmount,
                    'total_amount' => $itemTotal - $discountAmount,
                ]);

                // Update inventory for the user's assigned department
                $departmentId = auth()->user()->department_id;
                if (!$departmentId) {
                    throw new \Exception('User is not assigned to any department.');
                }
                // Convert to base units if UOM provided
                $baseQty = (int) $item['quantity'];
                if (!empty($item['uom_id'])) {
                    $uom = \App\Models\Uom::find($item['uom_id']);
                    $uomQty = (int) ($item['uom_quantity'] ?? 1);
                    if ($uom) { $baseQty = $uomQty * max(1, (int) $uom->units_per_uom); }
                }
                $inventoryService->adjustStockOut(
                    (int) $item['product_id'],
                    (int) $departmentId,
                    (int) $baseQty,
                    [
                        'movement_type' => 'out',
                        'reference_type' => 'sale',
                        'reference_id' => $sale->id,
                        'reference_number' => $sale->sale_number,
                        'notes' => 'Sale',
                    ]
                );
            }

            DB::commit();

            return redirect()->route('sales.show', $sale)
                ->with('success', 'Sale created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to create sale. Please try again.']);
        }
    }

    public function edit(Sale $sale)
    {
        if (!$sale->canBeEdited()) {
            return redirect()->route('sales.show', $sale)
                ->withErrors(['error' => 'This sale cannot be edited.']);
        }

        $sale->load(['customer', 'items.product']);
        $customers = Customer::ordered()->get();
        $products = Product::ordered()->get();

        return view('sales.edit', compact('sale', 'customers', 'products'));
    }

    public function update(Request $request, Sale $sale, InventoryService $inventoryService)
    {
        if (!$sale->canBeEdited()) {
            return redirect()->route('sales.show', $sale)
                ->withErrors(['error' => 'This sale cannot be edited.']);
        }

        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'sale_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:sale_date',
            'payment_term_id' => 'nullable|exists:payment_terms,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_percentage' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            // Restore inventory for old items to the user's department
            $departmentId = auth()->user()->department_id;
            if (!$departmentId) {
                throw new \Exception('User is not assigned to any department.');
            }
            foreach ($sale->items as $item) {
                $inventoryService->adjustStockIn(
                    (int) $item->product_id,
                    (int) $departmentId,
                    (int) $item->quantity,
                    [
                        'movement_type' => 'in',
                        'reference_type' => 'sale_reversal',
                        'reference_id' => $sale->id,
                        'reference_number' => $sale->sale_number,
                        'notes' => 'Sale updated - reversal of previous items',
                    ]
                );
            }

            // Delete old sale items
            $sale->items()->delete();

            // Calculate new totals
            $subtotal = 0;
            $totalDiscount = 0;
            
            foreach ($request->items as $item) {
                $itemTotal = $item['quantity'] * $item['unit_price'];
                $discountAmount = $itemTotal * ($item['discount_percentage'] ?? 0) / 100;
                $subtotal += $itemTotal;
                $totalDiscount += $discountAmount;
            }

            $totalAmount = $subtotal - $totalDiscount;

            // Recompute due date from terms if provided
            $dueDate = $request->due_date;
            if ($request->filled('payment_term_id')) {
                $term = \App\Models\PaymentTerm::find($request->payment_term_id);
                if ($term) {
                    if ($term->type === 'days') {
                        $dueDate = \Carbon\Carbon::parse($request->sale_date)->addDays($term->days)->toDateString();
                    } elseif ($term->type === 'eom') {
                        $dueDate = \Carbon\Carbon::parse($request->sale_date)->endOfMonth()->toDateString();
                    }
                }
            }

            // Update sale
            $sale->update([
                'customer_id' => $request->customer_id,
                'sale_date' => $request->sale_date,
                'due_date' => $dueDate,
                'subtotal' => $subtotal,
                'discount_amount' => $totalDiscount,
                'total_amount' => $totalAmount,
                'notes' => $request->notes,
                'payment_term_id' => $request->payment_term_id,
            ]);

            // Create new sale items
            foreach ($request->items as $item) {
                $itemTotal = $item['quantity'] * $item['unit_price'];
                $discountAmount = $itemTotal * ($item['discount_percentage'] ?? 0) / 100;
                
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'uom_id' => $item['uom_id'] ?? null,
                    'uom_quantity' => $item['uom_quantity'] ?? 1,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount_percentage' => $item['discount_percentage'] ?? 0,
                    'discount_amount' => $discountAmount,
                    'total_amount' => $itemTotal - $discountAmount,
                ]);

                // Update inventory for new items
                $baseQty = (int) $item['quantity'];
                if (!empty($item['uom_id'])) {
                    $uom = \App\Models\Uom::find($item['uom_id']);
                    $uomQty = (int) ($item['uom_quantity'] ?? 1);
                    if ($uom) { $baseQty = $uomQty * max(1, (int) $uom->units_per_uom); }
                }
                $inventoryService->adjustStockOut(
                    (int) $item['product_id'],
                    (int) $departmentId,
                    (int) $baseQty,
                    [
                        'movement_type' => 'out',
                        'reference_type' => 'sale',
                        'reference_id' => $sale->id,
                        'reference_number' => $sale->sale_number,
                        'notes' => 'Sale updated - new items',
                    ]
                );
            }

            DB::commit();

            return redirect()->route('sales.show', $sale)
                ->with('success', 'Sale updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to update sale. Please try again.']);
        }
    }

    public function destroy(Sale $sale, InventoryService $inventoryService)
    {
        if (!$sale->canBeEdited()) {
            return redirect()->route('sales.show', $sale)
                ->withErrors(['error' => 'This sale cannot be deleted.']);
        }

        DB::beginTransaction();
        try {
            // Restore inventory to the user's department
            $departmentId = auth()->user()->department_id;
            if (!$departmentId) {
                throw new \Exception('User is not assigned to any department.');
            }
            foreach ($sale->items as $item) {
                $inventoryService->adjustStockIn(
                    (int) $item->product_id,
                    (int) $departmentId,
                    (int) $item->quantity,
                    [
                        'movement_type' => 'in',
                        'reference_type' => 'sale_deletion',
                        'reference_id' => $sale->id,
                        'reference_number' => $sale->sale_number,
                        'notes' => 'Sale deleted',
                    ]
                );
            }

            $sale->delete();

            DB::commit();

            return redirect()->route('sales.index')
                ->with('success', 'Sale deleted successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to delete sale. Please try again.']);
        }
    }

    public function convertFromOrder(Order $order, InventoryService $inventoryService)
    {
        if ($order->order_status !== 'confirmed') {
            return redirect()->route('orders.show', $order)
                ->withErrors(['error' => 'Only confirmed orders can be converted to sales.']);
        }

        DB::beginTransaction();
        try {
            // Create sale from order
            $sale = Sale::create([
                'customer_id' => $order->customer_id,
                'sale_date' => now(),
                'due_date' => $order->delivery_date,
                'subtotal' => $order->subtotal,
                'discount_amount' => $order->discount_amount,
                'total_amount' => $order->total_amount,
                'notes' => "Converted from Order #{$order->order_number}",
                'status' => 'confirmed',
                'payment_status' => 'pending',
                'created_by' => auth()->id(),
            ]);

            // Create sale items from order items
            $departmentId = auth()->user()->department_id;
            if (!$departmentId) {
                throw new \Exception('User is not assigned to any department.');
            }
            foreach ($order->orderItems as $orderItem) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $orderItem->product_id,
                    'quantity' => $orderItem->quantity,
                    'unit_price' => $orderItem->unit_price,
                    'discount_percentage' => $orderItem->discount_percentage,
                    'discount_amount' => $orderItem->discount_amount,
                    'total_amount' => $orderItem->total_amount,
                ]);

                // Update inventory from the user's department
                $inventoryService->adjustStockOut(
                    (int) $orderItem->product_id,
                    (int) $departmentId,
                    (int) $orderItem->quantity,
                    [
                        'movement_type' => 'out',
                        'reference_type' => 'sale_from_order',
                        'reference_id' => $sale->id,
                        'reference_number' => $sale->sale_number,
                        'notes' => 'Converted from order',
                    ]
                );
            }

            // Update order status
            $order->update(['order_status' => 'converted']);

            DB::commit();

            return redirect()->route('sales.show', $sale)
                ->with('success', 'Order converted to sale successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to convert order to sale. Please try again.']);
        }
    }

    // Removed legacy updateInventory; InventoryService is now used consistently
}

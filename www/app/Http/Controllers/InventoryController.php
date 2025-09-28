<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Department;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Concerns\HasBreadcrumbs;

class InventoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->middleware('permission:inventory.view')->only(['index', 'show', 'stockReport']);
        $this->middleware('permission:inventory.create')->only(['create', 'store']);
        $this->middleware('permission:inventory.edit')->only(['edit', 'update']);
        $this->middleware('permission:inventory.delete')->only(['destroy']);
        $this->middleware('permission:inventory.stock_movement')->only(['stockMovement']);
        $this->middleware('permission:inventory.low_stock')->only(['lowStock']);
        $this->middleware('permission:inventory.stock_report')->only(['stockReport']);
    }
    use HasBreadcrumbs;
    public function index(Request $request)
    {
        $query = Inventory::with(['product', 'department'])
            ->join('products', 'products.id', '=', 'inventory.product_id')
            ->select('inventory.*');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('stockref', 'like', "%{$search}%");
            });
        }

        // Filter by department
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // Filter by stock level
        if ($request->filled('stock_level')) {
            switch ($request->stock_level) {
                case 'low':
                    $query->whereRaw('current_stock <= min_stock_level');
                    break;
                case 'out':
                    $query->where('current_stock', 0);
                    break;
                case 'available':
                    $query->where('current_stock', '>', 0);
                    break;
            }
        }

        $inventory = $query->orderBy('products.name', 'asc')->paginate(20);
        $departments = Department::active()->ordered()->get();

        $breadcrumbs = $this->setBreadcrumbs('inventory.index');

        return view('inventory.index', compact('inventory', 'departments') + $breadcrumbs);
    }

    public function show(Inventory $inventory)
    {
        $inventory->load(['product', 'department']);
        
        // Load stock movements manually to avoid relationship issues
        $stockMovements = $inventory->stockMovements()->orderBy('created_at', 'desc')->limit(10)->get();

        $breadcrumbs = $this->setBreadcrumbs('inventory.show', ['inventory' => $inventory]);

        return view('inventory.show', compact('inventory', 'stockMovements') + $breadcrumbs);
    }

    public function create()
    {
        $products = Product::ordered()->get();
        $departments = Department::ordered()->get();

        return view('inventory.create', compact('products', 'departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'department_id' => 'required|exists:departments,id',
            'current_stock' => 'required|integer|min:0',
            'min_stock_level' => 'required|integer|min:0',
            'max_stock_level' => 'nullable|integer|min:0',
            'reorder_point' => 'nullable|integer|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Check if inventory already exists for this product-department combination
            $existingInventory = Inventory::where('product_id', $request->product_id)
                ->where('department_id', $request->department_id)
                ->first();

            if ($existingInventory) {
                return back()->withErrors(['product_id' => 'Inventory already exists for this product in the selected department.']);
            }

            $inventory = Inventory::create([
                'product_id' => $request->product_id,
                'department_id' => $request->department_id,
                'current_stock' => $request->current_stock,
                'min_stock_level' => $request->min_stock_level,
                'max_stock_level' => $request->max_stock_level,
                'reorder_point' => $request->reorder_point,
                'cost_price' => $request->cost_price,
                'selling_price' => $request->selling_price,
                'created_by' => auth()->id(),
            ]);

            // Create initial stock movement
            if ($request->current_stock > 0) {
                StockMovement::create([
                    'inventory_id' => $inventory->id,
                    'movement_type' => 'initial',
                    'quantity' => $request->current_stock,
                    'reference_type' => 'inventory',
                    'reference_id' => $inventory->id,
                    'notes' => 'Initial stock entry',
                    'created_by' => auth()->id(),
                ]);
            }

            DB::commit();

            return redirect()->route('inventory.show', $inventory)
                ->with('success', 'Inventory created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to create inventory. Please try again.']);
        }
    }

    public function edit(Inventory $inventory)
    {
        $products = Product::ordered()->get();
        $departments = Department::ordered()->get();

        return view('inventory.edit', compact('inventory', 'products', 'departments'));
    }

    public function update(Request $request, Inventory $inventory)
    {
        $request->validate([
            'current_stock' => 'required|integer|min:0',
            'min_stock_level' => 'required|integer|min:0',
            'max_stock_level' => 'nullable|integer|min:0',
            'reorder_point' => 'nullable|integer|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
        ]);

        $oldStock = $inventory->current_stock;
        $newStock = $request->current_stock;
        $stockDifference = $newStock - $oldStock;

        DB::beginTransaction();
        try {
            $inventory->update([
                'current_stock' => $request->current_stock,
                'min_stock_level' => $request->min_stock_level,
                'max_stock_level' => $request->max_stock_level,
                'reorder_point' => $request->reorder_point,
                'cost_price' => $request->cost_price,
                'selling_price' => $request->selling_price,
            ]);

            // Create stock movement if stock changed
            if ($stockDifference != 0) {
                StockMovement::create([
                    'inventory_id' => $inventory->id,
                    'movement_type' => $stockDifference > 0 ? 'adjustment_in' : 'adjustment_out',
                    'quantity' => abs($stockDifference),
                    'reference_type' => 'inventory',
                    'reference_id' => $inventory->id,
                    'notes' => 'Stock adjustment',
                    'created_by' => auth()->id(),
                ]);
            }

            DB::commit();

            return redirect()->route('inventory.show', $inventory)
                ->with('success', 'Inventory updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to update inventory. Please try again.']);
        }
    }

    public function destroy(Inventory $inventory)
    {
        try {
            $inventory->delete();
            return redirect()->route('inventory.index')
                ->with('success', 'Inventory deleted successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete inventory. Please try again.']);
        }
    }

    public function stockMovement(Request $request, Inventory $inventory)
    {
        $request->validate([
            'movement_type' => 'required|in:in,out,transfer',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
            'reference_type' => 'nullable|string',
            'reference_id' => 'nullable|integer',
        ]);

        DB::beginTransaction();
        try {
            $quantity = $request->quantity;
            $movementType = $request->movement_type;

            // Update stock based on movement type
            if ($movementType === 'in') {
                $inventory->increment('current_stock', $quantity);
            } elseif ($movementType === 'out') {
                if ($inventory->current_stock < $quantity) {
                    return back()->withErrors(['quantity' => 'Insufficient stock. Available: ' . $inventory->current_stock]);
                }
                $inventory->decrement('current_stock', $quantity);
            }

            // Create stock movement record (movement table uses product/department)
            StockMovement::create([
                'product_id' => $inventory->product_id,
                'department_id' => $inventory->department_id,
                'movement_type' => $movementType,
                'quantity' => $quantity,
                'reference_type' => $request->reference_type,
                'reference_id' => $request->reference_id,
                'notes' => $request->notes,
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            return back()->with('success', 'Stock movement recorded successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to record stock movement: ' . $e->getMessage()]);
        }
    }

    public function lowStock()
    {
        $lowStockItems = Inventory::with(['product', 'department'])
            ->join('products', 'products.id', '=', 'inventory.product_id')
            ->select('inventory.*')
            ->whereRaw('current_stock <= min_stock_level')
            ->orderBy('products.name', 'asc')
            ->get();

        return view('inventory.low-stock', compact('lowStockItems'));
    }

    public function stockReport(Request $request)
    {
        $query = Inventory::with(['product', 'department']);

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        $inventory = $query->orderBy('product_id')->get();
        $departments = Department::ordered()->get();

        return view('inventory.stock-report', compact('inventory', 'departments'));
    }
}

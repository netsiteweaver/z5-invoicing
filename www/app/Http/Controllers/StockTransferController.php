<?php

namespace App\Http\Controllers;

use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Models\Department;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Concerns\HasBreadcrumbs;

class StockTransferController extends Controller
{
    use HasBreadcrumbs;
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->middleware('permission:stock_transfers.view')->only(['index', 'show']);
        $this->middleware('permission:stock_transfers.create')->only(['create', 'store']);
        $this->middleware('permission:stock_transfers.edit')->only(['edit', 'update']);
        $this->middleware('permission:stock_transfers.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = StockTransfer::with(['fromDepartment', 'toDepartment'])->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $transfers = $query->paginate(20);
        $departments = Department::ordered()->get();

        $breadcrumbs = $this->setBreadcrumbs('stock-transfers.index');
        return view('stock-transfers.index', compact('transfers', 'departments') + $breadcrumbs);
    }

    public function create()
    {
        $departments = Department::ordered()->get();
        $products = Product::ordered()->get();
        
        $breadcrumbs = $this->setBreadcrumbs('stock-transfers.create');
        return view('stock-transfers.create', compact('departments', 'products') + $breadcrumbs);
    }

    public function store(Request $request)
    {
        $request->validate([
            'from_department_id' => 'required|exists:departments,id|different:to_department_id',
            'to_department_id' => 'required|exists:departments,id',
            'transfer_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $transfer = StockTransfer::create([
                'uuid' => Str::uuid(),
                // transfer_number auto-generated in model using Params
                'from_department_id' => $request->from_department_id,
                'to_department_id' => $request->to_department_id,
                'transfer_date' => $request->transfer_date,
                'status' => 'approved',
                'notes' => $request->notes,
                'created_by' => auth()->id(),
            ]);

            foreach ($request->items as $item) {
                // Just create items now; stock moves happen on receive
                $transferItem = StockTransferItem::create([
                    'stock_transfer_id' => $transfer->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            $transfer->update(['status' => 'in_transit']);

            DB::commit();
            return redirect()->route('stock-transfers.show', $transfer)->with('success', 'Stock transfer created and set to In Transit.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create transfer: ' . $e->getMessage()])->withInput();
        }
    }

    public function show(StockTransfer $stock_transfer)
    {
        $stock_transfer->load(['items.product', 'fromDepartment', 'toDepartment']);
        
        $breadcrumbs = $this->setBreadcrumbs('stock-transfers.show', ['transfer' => $stock_transfer]);
        return view('stock-transfers.show', ['transfer' => $stock_transfer] + $breadcrumbs);
    }

    public function edit(StockTransfer $stock_transfer)
    {
        $stock_transfer->load('items');
        $departments = Department::ordered()->get();
        $products = Product::ordered()->get();
        
        $breadcrumbs = $this->setBreadcrumbs('stock-transfers.edit', ['transfer' => $stock_transfer]);
        return view('stock-transfers.edit', ['transfer' => $stock_transfer, 'departments' => $departments, 'products' => $products] + $breadcrumbs);
    }

    public function update(Request $request, StockTransfer $stock_transfer)
    {
        $request->validate([
            'from_department_id' => 'required|exists:departments,id',
            'to_department_id' => 'required|exists:departments,id',
            'transfer_date' => 'required|date',
            'status' => 'required|in:draft,requested,approved,in_transit,received,cancelled',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            // Update the transfer basic info
            $stock_transfer->update($request->only([
                'from_department_id', 
                'to_department_id', 
                'transfer_date', 
                'status', 
                'notes'
            ]));

            // Handle items - delete existing and create new ones
            $stock_transfer->items()->delete();
            
            foreach ($request->items as $itemData) {
                StockTransferItem::create([
                    'stock_transfer_id' => $stock_transfer->id,
                    'product_id' => $itemData['product_id'],
                    'quantity' => $itemData['quantity'],
                ]);
            }

            DB::commit();
            return redirect()->route('stock-transfers.show', $stock_transfer)->with('success', 'Transfer updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update transfer: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy(StockTransfer $stock_transfer)
    {
        try {
            $stock_transfer->delete();
            return redirect()->route('stock-transfers.index')->with('success', 'Transfer deleted.');
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => 'Failed to delete: ' . $e->getMessage()]);
        }
    }

    public function receive(StockTransfer $stock_transfer)
    {
        $this->middleware('permission:stock_transfers.approve');
        if ($stock_transfer->status === 'received') {
            return back()->with('success', 'Already received');
        }
        DB::beginTransaction();
        try {
            $stock_transfer->load('items');
            foreach ($stock_transfer->items as $item) {
                // Deduct from source
                $sourceInventory = \App\Models\Inventory::where('product_id', $item->product_id)
                    ->where('department_id', $stock_transfer->from_department_id)
                    ->lockForUpdate()
                    ->first();
                if (!$sourceInventory || $sourceInventory->current_stock < $item->quantity) {
                    throw new \RuntimeException('Insufficient stock at source for product ID ' . $item->product_id);
                }
                $sourceInventory->decrement('current_stock', $item->quantity);
                StockMovement::create([
                    'product_id' => $item->product_id,
                    'department_id' => $stock_transfer->from_department_id,
                    'movement_type' => 'transfer_out',
                    'quantity' => $item->quantity,
                    'reference_type' => 'stock_transfer',
                    'reference_id' => $item->id,
                    'reference_number' => $stock_transfer->transfer_number,
                    'notes' => 'Transfer out (receive)',
                    'created_by' => auth()->id(),
                ]);

                // Add to destination
                $destInventory = \App\Models\Inventory::firstOrCreate([
                    'product_id' => $item->product_id,
                    'department_id' => $stock_transfer->to_department_id,
                ], [
                    'current_stock' => 0,
                    'min_stock_level' => 0,
                    'reorder_point' => 0,
                    'created_by' => auth()->id(),
                ]);
                $destInventory->increment('current_stock', $item->quantity);
                StockMovement::create([
                    'product_id' => $item->product_id,
                    'department_id' => $stock_transfer->to_department_id,
                    'movement_type' => 'transfer_in',
                    'quantity' => $item->quantity,
                    'reference_type' => 'stock_transfer',
                    'reference_id' => $item->id,
                    'reference_number' => $stock_transfer->transfer_number,
                    'notes' => 'Transfer in (receive)',
                    'created_by' => auth()->id(),
                ]);
            }

            $stock_transfer->update(['status' => 'received', 'received_by' => auth()->id()]);
            DB::commit();
            return back()->with('success', 'Transfer received and stock updated.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Receive failed: ' . $e->getMessage()]);
        }
    }

    public function print(StockTransfer $stock_transfer)
    {
        $stock_transfer->load(['items.product', 'fromDepartment', 'toDepartment']);
        $pdf = Pdf::loadView('stock-transfers.print', ['transfer' => $stock_transfer]);
        return $pdf->download($stock_transfer->transfer_number . '.pdf');
    }
}



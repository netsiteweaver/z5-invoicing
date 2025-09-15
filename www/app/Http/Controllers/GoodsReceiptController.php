<?php

namespace App\Http\Controllers;

use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use App\Models\Product;
use App\Models\Department;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class GoodsReceiptController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->middleware('permission:goods_receipts.view')->only(['index', 'show']);
        $this->middleware('permission:goods_receipts.create')->only(['create', 'store']);
        $this->middleware('permission:goods_receipts.edit')->only(['edit', 'update']);
        $this->middleware('permission:goods_receipts.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = GoodsReceipt::with(['department'])->orderByDesc('created_at');

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('grn_number', 'like', "%{$s}%")
                  ->orWhere('supplier_name', 'like', "%{$s}%")
                  ->orWhere('supplier_ref', 'like', "%{$s}%");
            });
        }

        $receipts = $query->paginate(20);
        $departments = Department::ordered()->get();

        $breadcrumbs = [
            ['title' => 'Inventory', 'url' => route('inventory.index'), 'current' => false],
            ['title' => 'Goods Receipts', 'url' => null, 'current' => true],
        ];
        return view('goods-receipts.index', compact('receipts', 'departments', 'breadcrumbs'));
    }

    public function create()
    {
        $departments = Department::ordered()->get();
        $products = Product::ordered()->get();
        $breadcrumbs = [
            ['title' => 'Inventory', 'url' => route('inventory.index'), 'current' => false],
            ['title' => 'Goods Receipts', 'url' => route('goods-receipts.index'), 'current' => false],
            ['title' => 'Create Receipt', 'url' => null, 'current' => true],
        ];
        return view('goods-receipts.create', compact('departments', 'products', 'breadcrumbs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'receipt_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_cost' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $receipt = GoodsReceipt::create([
                'uuid' => Str::uuid(),
                // grn_number auto-generated in model using Params
                'department_id' => $request->department_id,
                'receipt_date' => $request->receipt_date,
                'supplier_name' => $request->supplier_name,
                'supplier_ref' => $request->supplier_ref,
                'container_no' => $request->container_no,
                'bill_of_lading' => $request->bill_of_lading,
                'notes' => $request->notes,
                'created_by' => auth()->id(),
                'approval_status' => 'submitted',
            ]);

            foreach ($request->items as $item) {
                $receiptItem = GoodsReceiptItem::create([
                    'goods_receipt_id' => $receipt->id,
                    'product_id' => $item['product_id'],
                    'department_id' => $request->department_id,
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'] ?? null,
                    'uom' => $item['uom'] ?? null,
                    'batch_no' => $item['batch_no'] ?? null,
                    'expiry_date' => $item['expiry_date'] ?? null,
                    'notes' => $item['notes'] ?? null,
                ]);

                // Defer stock changes until approval
            }

            DB::commit();
            return redirect()->route('goods-receipts.show', $receipt)->with('success', 'Goods received successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create goods receipt: ' . $e->getMessage()])->withInput();
        }
    }

    public function show(GoodsReceipt $goods_receipt)
    {
        $goods_receipt->load(['items.product', 'department']);
        $breadcrumbs = [
            ['title' => 'Inventory', 'url' => route('inventory.index'), 'current' => false],
            ['title' => 'Goods Receipts', 'url' => route('goods-receipts.index'), 'current' => false],
            ['title' => $goods_receipt->grn_number, 'url' => null, 'current' => true],
        ];
        return view('goods-receipts.show', ['receipt' => $goods_receipt, 'breadcrumbs' => $breadcrumbs]);
    }

    public function edit(GoodsReceipt $goods_receipt)
    {
        $goods_receipt->load(['items']);
        $departments = Department::ordered()->get();
        $products = Product::ordered()->get();
        $breadcrumbs = [
            ['title' => 'Inventory', 'url' => route('inventory.index'), 'current' => false],
            ['title' => 'Goods Receipts', 'url' => route('goods-receipts.index'), 'current' => false],
            ['title' => $goods_receipt->grn_number, 'url' => route('goods-receipts.show', $goods_receipt), 'current' => false],
            ['title' => 'Edit Receipt', 'url' => null, 'current' => true],
        ];
        return view('goods-receipts.edit', ['receipt' => $goods_receipt, 'departments' => $departments, 'products' => $products, 'breadcrumbs' => $breadcrumbs]);
    }

    public function update(Request $request, GoodsReceipt $goods_receipt)
    {
        $request->validate([
            'receipt_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $goods_receipt->update($request->only(['receipt_date', 'supplier_name', 'supplier_ref', 'container_no', 'bill_of_lading', 'notes']));
        return redirect()->route('goods-receipts.show', $goods_receipt)->with('success', 'Goods receipt updated.');
    }

    public function destroy(GoodsReceipt $goods_receipt)
    {
        try {
            $goods_receipt->delete();
            return redirect()->route('goods-receipts.index')->with('success', 'Goods receipt deleted.');
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => 'Failed to delete: ' . $e->getMessage()]);
        }
    }

    public function print(GoodsReceipt $goods_receipt)
    {
        $goods_receipt->load(['items.product', 'department']);
        $pdf = Pdf::loadView('goods-receipts.print', ['receipt' => $goods_receipt]);
        return $pdf->download($goods_receipt->grn_number . '.pdf');
    }

    public function approve(GoodsReceipt $goods_receipt)
    {
        $this->middleware('permission:goods_receipts.approve');
        if ($goods_receipt->approval_status === 'approved') {
            return back()->with('success', 'Already approved');
        }
        DB::beginTransaction();
        try {
            $goods_receipt->load('items');
            foreach ($goods_receipt->items as $item) {
                // Update inventory and record movement
                $inventory = \App\Models\Inventory::firstOrCreate([
                    'product_id' => $item->product_id,
                    'department_id' => $goods_receipt->department_id,
                ], [
                    'current_stock' => 0,
                    'min_stock_level' => 0,
                    'reorder_point' => 0,
                    'created_by' => auth()->id(),
                ]);
                $inventory->increment('current_stock', $item->quantity);

                StockMovement::create([
                    'product_id' => $item->product_id,
                    'department_id' => $goods_receipt->department_id,
                    'movement_type' => 'in',
                    'quantity' => $item->quantity,
                    'reference_type' => 'goods_receipt',
                    'reference_id' => $item->id,
                    'reference_number' => $goods_receipt->grn_number,
                    'notes' => 'GRN approved',
                    'created_by' => auth()->id(),
                ]);
            }
            $goods_receipt->update(['approval_status' => 'approved', 'updated_by' => auth()->id()]);
            DB::commit();
            return back()->with('success', 'Goods receipt approved and stock updated.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Approval failed: ' . $e->getMessage()]);
        }
    }
}



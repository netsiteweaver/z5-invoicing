<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Sale;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Concerns\HasBreadcrumbs;

class InvoiceController extends Controller
{
	use HasBreadcrumbs;

	public function __construct()
	{
		$this->middleware(['auth', 'verified']);
		$this->middleware('permission:invoices.view')->only(['index', 'show']);
		$this->middleware('permission:invoices.create')->only(['create', 'store', 'createFromSale']);
		$this->middleware('permission:invoices.edit')->only(['edit', 'update']);
		$this->middleware('permission:invoices.delete')->only(['destroy']);
	}

	public function index(Request $request)
	{
		$query = Invoice::with(['customer', 'sale']);

		if ($request->filled('search')) {
			$search = $request->get('search');
			$query->where(function ($q) use ($search) {
				$q->where('invoice_number', 'like', "%{$search}%")
					->orWhereHas('customer', function ($cq) use ($search) {
						$cq->where('company_name', 'like', "%{$search}%")
							->orWhere('full_name', 'like', "%{$search}%");
					});
			});
		}

		$invoices = $query->orderByDesc('id')->paginate(20);
		$customers = Customer::ordered()->get();
		$breadcrumbs = $this->setBreadcrumbs('invoices.index');
		return view('invoices.index', compact('invoices', 'customers') + $breadcrumbs);
	}

	public function show(Invoice $invoice)
	{
		$invoice->load(['customer', 'sale', 'items']);
		$breadcrumbs = $this->setBreadcrumbs('invoices.show', ['invoice' => $invoice]);
		return view('invoices.show', compact('invoice') + $breadcrumbs);
	}

	public function createFromSale(Sale $sale)
	{
		$this->authorize('create', Invoice::class);
		$sale->load(['items.product', 'customer']);

		DB::beginTransaction();
		try {
			$invoice = Invoice::create([
				'customer_id' => $sale->customer_id,
				'sale_id' => $sale->id,
				'invoice_date' => now()->toDateString(),
				'due_date' => $sale->due_date ?? now()->addDays(30)->toDateString(),
				'status' => 'sent',
				'payment_status' => 'pending',
				'subtotal' => $sale->subtotal,
				'tax_amount' => $sale->tax_amount ?? 0,
				'discount_amount' => $sale->discount_amount ?? 0,
				'total_amount' => $sale->total_amount,
				'paid_amount' => 0,
				'balance_amount' => $sale->total_amount,
				'notes' => "Invoice for Sale #{$sale->sale_number}",
				'created_by' => auth()->id(),
			]);

			foreach ($sale->items as $item) {
				InvoiceItem::create([
					'invoice_id' => $invoice->id,
					'product_id' => $item->product_id,
					'product_name' => $item->product->name ?? 'Product',
					'product_sku' => $item->product->sku ?? null,
					'description' => null,
					'quantity' => $item->quantity,
					'unit_price' => $item->unit_price,
					'discount_percentage' => $item->discount_percent ?? 0,
					'discount_amount' => $item->discount_amount ?? 0,
					'tax_percentage' => $item->tax_percent ?? 0,
					'tax_amount' => $item->tax_amount ?? 0,
					'line_total' => $item->line_total,
					'created_by' => auth()->id(),
				]);
			}

			DB::commit();
			return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice created from sale successfully.');
		} catch (\Throwable $e) {
			DB::rollBack();
			return redirect()->route('sales.show', $sale)->with('error', 'Failed to create invoice: ' . $e->getMessage());
		}
	}
}


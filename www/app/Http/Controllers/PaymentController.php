<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\PaymentType;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Concerns\HasBreadcrumbs;

class PaymentController extends Controller
{
	use HasBreadcrumbs;
	public function __construct()
	{
		$this->middleware(['auth', 'verified']);
		$this->middleware('permission:payments.view')->only(['index', 'show']);
		$this->middleware('permission:payments.create')->only(['create', 'store']);
	}

	public function index(Request $request)
	{
		$query = Payment::with(['sale', 'customer', 'paymentType']);

		if ($request->filled('search')) {
			$search = $request->search;
			$query->where(function ($q) use ($search) {
				$q->where('payment_number', 'like', "%{$search}%")
					->orWhere('reference_number', 'like', "%{$search}%")
					->orWhereHas('customer', function ($cq) use ($search) {
						$cq->where('company_name', 'like', "%{$search}%")
							->orWhere('full_name', 'like', "%{$search}%");
					});
			});
		}

		if ($request->filled('status')) {
			$query->where('payment_status', $request->status);
		}

		$payments = $query->orderByDesc('payment_date')->paginate(20);

		$breadcrumbs = $this->setBreadcrumbs('payments.index');

		return view('payments.index', compact('payments') + $breadcrumbs);
	}

	public function create(Request $request, Sale $sale = null)
	{
		// Sales with unpaid or partial status
		$sales = Sale::whereIn('payment_status', ['pending', 'partial', 'overdue'])
			->orderByDesc('created_at')
			->with(['customer'])
			->paginate(20);

		$preselectedSale = null;
		if ($sale) {
			$preselectedSale = $sale->load('customer');
		} elseif ($request->filled('sale_id')) {
			$preselectedSale = Sale::with('customer')->find($request->get('sale_id'));
		}

		$paymentTypes = PaymentType::orderBy('display_order')->orderBy('name')->get();

		$breadcrumbs = $this->setBreadcrumbs('payments.create');

		return view('payments.create', [
			'sales' => $sales,
			'paymentTypes' => $paymentTypes,
			'preselectedSale' => $preselectedSale,
		] + $breadcrumbs);
	}

	public function store(Request $request, Sale $sale = null)
	{
		$validated = $request->validate([
			'sale_id' => ['nullable', 'exists:sales,id'],
			'payment_date' => ['required', 'date'],
			'amount' => ['required', 'numeric', 'min:0.01'],
			'payment_type_id' => ['required', 'exists:payment_types,id'],
			'payment_method' => ['nullable', 'string', 'max:100'],
			'reference_number' => ['nullable', 'string', 'max:100'],
			'notes' => ['nullable', 'string', 'max:1000'],
		]);

		// If sale is provided via route parameter, use it; otherwise use sale_id from request
		if ($sale) {
			$validated['sale_id'] = $sale->id;
		} elseif (!$validated['sale_id']) {
			return back()->withErrors(['sale_id' => 'Sale is required.'])->withInput();
		}

		$sale = Sale::with('customer', 'payments')->findOrFail($validated['sale_id']);
		$paymentType = PaymentType::findOrFail($validated['payment_type_id']);

		// Compute amount due and block overpayment
		$totalPaid = (float) $sale->payments()->where('status', 1)->sum('amount');
		$due = max(0.0, (float) $sale->total_amount - $totalPaid);
		if ((float) $validated['amount'] > $due) {
			return back()->withErrors(['amount' => 'Payment amount exceeds amount due (Rs '.number_format($due, 2).').'])->withInput();
		}

		DB::beginTransaction();
		try {
			$payment = Payment::create([
				'payment_date' => $validated['payment_date'],
				'payment_type' => 'receipt',
				'payment_method' => $validated['payment_method'] ?? $paymentType->name,
				'amount' => $validated['amount'],
				'payment_type_id' => $paymentType->id,
				'sale_id' => $sale->id,
				'customer_id' => $sale->customer_id,
				'reference_number' => $validated['reference_number'] ?? null,
				'notes' => $validated['notes'] ?? null,
				'payment_status' => 'paid',
				'created_by' => auth()->id(),
				'updated_by' => null,
				'status' => 1,
			]);

			$this->updateSalePaymentStatus($sale->id);

			DB::commit();

			return redirect()->route('sales.show', $sale)
				->with('success', 'Payment recorded successfully.');
		} catch (\Throwable $e) {
			DB::rollBack();
			return back()->withErrors(['error' => 'Failed to record payment: '.$e->getMessage()])->withInput();
		}
	}

	public function show(Payment $payment)
	{
		$payment->load(['sale.customer', 'paymentType', 'createdBy']);
		
		$breadcrumbs = $this->setBreadcrumbs('payments.show', ['payment' => $payment]);
		
		return view('payments.show', compact('payment') + $breadcrumbs);
	}

	private function updateSalePaymentStatus(int $saleId): void
	{
		$sale = Sale::with(['payments' => function ($q) {
			$q->where('status', 1)->whereNotIn('payment_status', ['cancelled']);
		}])->findOrFail($saleId);

		$totalPaid = $sale->payments->sum('amount');
		$newStatus = 'pending';
		if ($totalPaid <= 0) {
			$newStatus = 'pending';
		} elseif ($totalPaid < (float) $sale->total_amount) {
			$newStatus = 'partial';
		} else {
			$newStatus = 'paid';
		}

		$sale->update(['payment_status' => $newStatus]);
	}
}


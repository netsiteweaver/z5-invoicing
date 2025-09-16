<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Sale;
use App\Models\Payment;
use App\Models\PaymentType;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->middleware('permission:sales.edit')->only(['storeForSale']);
    }

    public function storeForSale(Request $request, Sale $sale)
    {
        $validated = $request->validate([
            'payment_date' => ['required', 'date'],
            'payment_method' => ['required', 'string', 'max:100'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_type_id' => ['required', 'exists:payment_types,id'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::beginTransaction();
        try {
            $paymentType = PaymentType::find($validated['payment_type_id']);

            Payment::create([
                'payment_date' => $validated['payment_date'],
                'payment_method' => $validated['payment_method'],
                'amount' => $validated['amount'],
                'payment_type_id' => $paymentType->id,
                'payment_type' => 'receipt',
                'order_id' => null,
                'sale_id' => $sale->id,
                'customer_id' => $sale->customer_id,
                'reference_number' => $validated['reference_number'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'payment_status' => 'paid',
                'created_by' => auth()->id(),
                'status' => 1,
            ]);

            // Model events will refresh payment status
            DB::commit();
            return redirect()->route('sales.show', $sale)
                ->with('success', 'Payment recorded successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->route('sales.show', $sale)
                ->with('error', 'Failed to record payment: ' . $e->getMessage());
        }
    }
}


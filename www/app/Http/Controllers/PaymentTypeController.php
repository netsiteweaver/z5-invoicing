<?php

namespace App\Http\Controllers;

use App\Models\PaymentType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Concerns\HasBreadcrumbs;

class PaymentTypeController extends Controller
{
    use HasBreadcrumbs;

    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->middleware('permission:payment-types.view')->only(['index', 'show']);
        $this->middleware('permission:payment-types.create')->only(['create', 'store']);
        $this->middleware('permission:payment-types.edit')->only(['edit', 'update', 'updateOrder']);
        $this->middleware('permission:payment-types.delete')->only(['destroy']);
    }

    /**
     * Display a listing of payment types.
     */
    public function index(Request $request)
    {
        $query = PaymentType::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $paymentTypes = $query->ordered()->paginate(15);

        return view('payment-types.index', compact('paymentTypes'));
    }

    /**
     * Show the form for creating a new payment type.
     */
    public function create()
    {
        return view('payment-types.create');
    }

    /**
     * Store a newly created payment type in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_default' => 'boolean',
            'display_order' => 'integer|min:0',
            'status' => 'integer|in:0,1',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['updated_by'] = auth()->id();

        PaymentType::create($validated);

        return redirect()->route('payment-types.index')
            ->with('success', 'Payment type created successfully.');
    }

    /**
     * Display the specified payment type.
     */
    public function show(PaymentType $paymentType)
    {
        return view('payment-types.show', compact('paymentType'));
    }

    /**
     * Show the form for editing the specified payment type.
     */
    public function edit(PaymentType $paymentType)
    {
        return view('payment-types.edit', compact('paymentType'));
    }

    /**
     * Update the specified payment type in storage.
     */
    public function update(Request $request, PaymentType $paymentType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_default' => 'boolean',
            'display_order' => 'integer|min:0',
            'status' => 'integer|in:0,1',
        ]);

        $validated['updated_by'] = auth()->id();

        $paymentType->update($validated);

        return redirect()->route('payment-types.index')
            ->with('success', 'Payment type updated successfully.');
    }

    /**
     * Remove the specified payment type from storage.
     */
    public function destroy(PaymentType $paymentType)
    {
        // Check if payment type is being used
        if ($paymentType->payments()->count() > 0) {
            return redirect()->route('payment-types.index')
                ->with('error', 'Cannot delete payment type that is being used in payments.');
        }

        $paymentType->delete();

        return redirect()->route('payment-types.index')
            ->with('success', 'Payment type deleted successfully.');
    }

    /**
     * Update the order of payment types.
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*.id' => 'required|integer|exists:payment_types,id',
            'order.*.display_order' => 'required|integer|min:1',
        ]);

        try {
            foreach ($request->order as $item) {
                PaymentType::where('id', $item['id'])
                    ->update(['display_order' => $item['display_order']]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Payment type order updated successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update payment type order.'
            ], 500);
        }
    }
}

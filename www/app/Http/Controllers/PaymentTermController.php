<?php

namespace App\Http\Controllers;

use App\Models\PaymentTerm;
use Illuminate\Http\Request;
use App\Http\Controllers\Concerns\HasBreadcrumbs;

class PaymentTermController extends Controller
{
    use HasBreadcrumbs;

    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->middleware('permission:settings.view')->only(['index']);
        $this->middleware('permission:settings.edit')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index()
    {
        $terms = PaymentTerm::orderBy('is_default', 'desc')->orderBy('name')->paginate(20);
        
        $breadcrumbs = $this->setBreadcrumbs('payment-terms.index');
        
        return view('payment-terms.index', compact('terms') + $breadcrumbs);
    }

    public function show(PaymentTerm $payment_term)
    {
        $breadcrumbs = $this->setBreadcrumbs('payment-terms.show', ['payment_term' => $payment_term]);
        
        return view('payment-terms.show', compact('payment_term') + $breadcrumbs);
    }

    public function create()
    {
        $breadcrumbs = $this->setBreadcrumbs('payment-terms.create');
        
        return view('payment-terms.create', $breadcrumbs);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:days,eom,manual',
            'days' => 'nullable|integer|min:0',
            'description' => 'nullable|string|max:255',
            'is_default' => 'boolean',
        ]);
        if (($validated['type'] ?? 'days') === 'days' && ($validated['days'] === null)) {
            return back()->withErrors(['days' => 'Days is required for type days.'])->withInput();
        }
        if (!empty($validated['is_default'])) {
            PaymentTerm::query()->update(['is_default' => false]);
        }
        PaymentTerm::create($validated + ['created_by' => auth()->id()]);
        return redirect()->route('payment-terms.index')->with('success', 'Payment term created.');
    }

    public function edit(PaymentTerm $payment_term)
    {
        $breadcrumbs = $this->setBreadcrumbs('payment-terms.edit', ['payment_term' => $payment_term]);
        
        return view('payment-terms.edit', compact('payment_term') + $breadcrumbs);
    }

    public function update(Request $request, PaymentTerm $payment_term)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:days,eom,manual',
            'days' => 'nullable|integer|min:0',
            'description' => 'nullable|string|max:255',
            'is_default' => 'boolean',
            'status' => 'in:0,1',
        ]);
        if (($validated['type'] ?? 'days') === 'days' && ($validated['days'] === null)) {
            return back()->withErrors(['days' => 'Days is required for type days.'])->withInput();
        }
        if (!empty($validated['is_default'])) {
            PaymentTerm::query()->where('id', '!=', $payment_term->id)->update(['is_default' => false]);
        }
        $payment_term->update($validated + ['updated_by' => auth()->id()]);
        return redirect()->route('payment-terms.index')->with('success', 'Payment term updated.');
    }

    public function destroy(PaymentTerm $payment_term)
    {
        $payment_term->update(['status' => 0]);
        return redirect()->route('payment-terms.index')->with('success', 'Payment term deleted.');
    }
}




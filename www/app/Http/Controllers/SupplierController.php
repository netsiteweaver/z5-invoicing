<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Concerns\HasBreadcrumbs;

class SupplierController extends Controller
{
    use HasBreadcrumbs;
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index(Request $request)
    {
        $query = Supplier::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('phone_number1', 'like', "%{$s}%")
                  ->orWhere('contact_person', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', (int) $request->status);
        }

        $suppliers = $query->orderBy('name')->paginate(15);

        $breadcrumbs = $this->setBreadcrumbs('suppliers.index');

        return view('suppliers.index', compact('suppliers') + $breadcrumbs);
    }

    public function create()
    {
        $breadcrumbs = $this->setBreadcrumbs('suppliers.create');
        return view('suppliers.create', $breadcrumbs);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone_number1' => ['nullable', 'string', 'max:50'],
            'phone_number2' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'remarks' => ['nullable', 'string'],
        ]);

        $validated['created_by'] = auth()->id();
        $validated['status'] = 1;

        Supplier::create($validated);

        return redirect()->route('suppliers.index')->with('success', 'Supplier created successfully.');
    }

    public function show(Supplier $supplier)
    {
        $breadcrumbs = $this->setBreadcrumbs('suppliers.show', ['supplier' => $supplier]);
        return view('suppliers.show', compact('supplier') + $breadcrumbs);
    }

    public function edit(Supplier $supplier)
    {
        $breadcrumbs = $this->setBreadcrumbs('suppliers.edit', ['supplier' => $supplier]);
        return view('suppliers.edit', compact('supplier') + $breadcrumbs);
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone_number1' => ['nullable', 'string', 'max:50'],
            'phone_number2' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'remarks' => ['nullable', 'string'],
            'status' => ['nullable', Rule::in([0,1])],
        ]);

        $validated['updated_by'] = auth()->id();

        $supplier->update($validated);

        return redirect()->route('suppliers.index')->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->update(['status' => 0]);
        return redirect()->route('suppliers.index')->with('success', 'Supplier deleted successfully.');
    }
}


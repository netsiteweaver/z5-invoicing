<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Concerns\HasBreadcrumbs;

class CustomerController extends Controller
{
    use HasBreadcrumbs;

    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->middleware('permission:customers.view')->only(['index', 'show']);
        $this->middleware('permission:customers.create')->only(['create', 'store']);
        $this->middleware('permission:customers.edit')->only(['edit', 'update']);
        $this->middleware('permission:customers.delete')->only(['destroy']);
    }
    /**
     * Display a listing of customers.
     */
    public function index(Request $request)
    {
        $query = Customer::with(['createdBy', 'updatedBy']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone_number1', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        // Filter by customer type
        if ($request->filled('type')) {
            $query->where('customer_type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $customers = $query->active()->latest()->paginate(15);

        $breadcrumbs = $this->setBreadcrumbs('customers.index');

        return view('customers.index', compact('customers') + $breadcrumbs);
    }

    /**
     * Show the form for creating a new customer.
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * Store a newly created customer.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_type' => ['required', 'in:individual,business'],
            'full_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:customers,email'],
            'phone_number' => ['nullable', 'string', 'max:50'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:100'],
            'brn' => ['nullable', 'string', 'max:100'],
            'vat' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);

        // Conditional requirements
        if ($validated['customer_type'] === 'business') {
            // For business, company_name is the display name
            $request->validate(['company_name' => ['required', 'string', 'max:255']]);
        } else {
            // For individual, full_name is required
            $request->validate(['full_name' => ['required', 'string', 'max:255']]);
        }

        // Check for unique constraints - allow recreation of deleted customers
        $this->validateUniqueFields($request, null);

        $validated['created_by'] = auth()->id();
        $validated['status'] = 1;

        Customer::create($validated);

        return redirect()->route('customers.index')
            ->with('success', 'Customer created successfully.');
    }

    /**
     * Display the specified customer.
     */
    public function show(Customer $customer)
    {
        $customer->load(['orders.items.product', 'sales.items.product', 'createdBy', 'updatedBy']);
        
        $breadcrumbs = $this->setBreadcrumbs('customers.show', ['customer' => $customer]);
        
        return view('customers.show', compact('customer') + $breadcrumbs);
    }

    /**
     * Show the form for editing the specified customer.
     */
    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    /**
     * Update the specified customer.
     */
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'customer_type' => ['required', 'in:individual,business'],
            'full_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('customers', 'email')->ignore($customer->id)],
            'phone_number' => ['nullable', 'string', 'max:50'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:100'],
            'brn' => ['nullable', 'string', 'max:100'],
            'vat' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);

        // Conditional requirements
        if ($validated['customer_type'] === 'business') {
            $request->validate(['company_name' => ['required', 'string', 'max:255']]);
        } else {
            $request->validate(['full_name' => ['required', 'string', 'max:255']]);
        }

        // Check for unique constraints - allow recreation of deleted customers
        $this->validateUniqueFields($request, $customer);

        $validated['updated_by'] = auth()->id();

        $customer->update($validated);

        return redirect()->route('customers.index')
            ->with('success', 'Customer updated successfully.');
    }

    /**
     * Remove the specified customer from storage.
     */
    public function destroy(Customer $customer)
    {
        $customer->update(['status' => 0]);

        return redirect()->route('customers.index')
            ->with('success', 'Customer deleted successfully.');
    }

    /**
     * Validate unique fields - allow recreation of deleted customers
     */
    private function validateUniqueFields(Request $request, ?Customer $customer = null)
    {
        $errors = [];

        // Check company name uniqueness (only among active customers)
        if ($request->filled('company_name')) {
            $query = Customer::where('company_name', $request->company_name)
                            ->where('status', 1); // Only check active customers
            if ($customer) {
                $query->where('id', '!=', $customer->id);
            }
            $existing = $query->first();
            
            if ($existing) {
                $errors['company_name'] = 'A customer with this company name already exists.';
            }
        }

        // Check BRN uniqueness (only among active customers)
        if ($request->filled('brn')) {
            $query = Customer::where('brn', $request->brn)
                            ->where('status', 1); // Only check active customers
            if ($customer) {
                $query->where('id', '!=', $customer->id);
            }
            $existing = $query->first();
            
            if ($existing) {
                $errors['brn'] = 'A customer with this BRN already exists.';
            }
        }

        // Check VAT uniqueness (only among active customers)
        if ($request->filled('vat')) {
            $query = Customer::where('vat', $request->vat)
                            ->where('status', 1); // Only check active customers
            if ($customer) {
                $query->where('id', '!=', $customer->id);
            }
            $existing = $query->first();
            
            if ($existing) {
                $errors['vat'] = 'A customer with this VAT number already exists.';
            }
        }

        if (!empty($errors)) {
            throw \Illuminate\Validation\ValidationException::withMessages($errors);
        }
    }
}

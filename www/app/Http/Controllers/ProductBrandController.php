<?php

namespace App\Http\Controllers;

use App\Models\ProductBrand;
use Illuminate\Http\Request;
use App\Http\Controllers\Concerns\HasBreadcrumbs;

class ProductBrandController extends Controller
{
    use HasBreadcrumbs;
    /**
     * Display a listing of product brands.
     */
    public function index(Request $request)
    {
        $query = ProductBrand::with(['products']);

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

        $brands = $query->active()->ordered()->paginate(15);

        $breadcrumbs = $this->setBreadcrumbs('product-brands.index');

        return view('product-brands.index', compact('brands') + $breadcrumbs);
    }

    /**
     * Show the form for creating a new product brand.
     */
    public function create()
    {
        $breadcrumbs = $this->setBreadcrumbs('product-brands.create');
        
        return view('product-brands.create', $breadcrumbs);
    }

    /**
     * Store a newly created product brand.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'website' => ['nullable', 'url'],
            'contact_email' => ['nullable', 'email'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['created_by'] = auth()->id();
        $validated['status'] = 1;

        ProductBrand::create($validated);

        return redirect()->route('product-brands.index')
            ->with('success', 'Product brand created successfully.');
    }

    /**
     * Display the specified product brand.
     */
    public function show(ProductBrand $productBrand)
    {
        $productBrand->load(['products.category', 'createdBy', 'updatedBy']);
        
        $breadcrumbs = $this->setBreadcrumbs('product-brands.show', ['productBrand' => $productBrand]);
        
        return view('product-brands.show', compact('productBrand') + $breadcrumbs);
    }

    /**
     * Show the form for editing the specified product brand.
     */
    public function edit(ProductBrand $productBrand)
    {
        $breadcrumbs = $this->setBreadcrumbs('product-brands.edit', ['productBrand' => $productBrand]);
        
        return view('product-brands.edit', compact('productBrand') + $breadcrumbs);
    }

    /**
     * Update the specified product brand.
     */
    public function update(Request $request, ProductBrand $productBrand)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'website' => ['nullable', 'url'],
            'contact_email' => ['nullable', 'email'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['updated_by'] = auth()->id();

        $productBrand->update($validated);

        return redirect()->route('product-brands.index')
            ->with('success', 'Product brand updated successfully.');
    }

    /**
     * Remove the specified product brand from storage.
     */
    public function destroy(ProductBrand $productBrand)
    {
        // Soft delete (status flag is still set to 0 for consistency)
        if ($productBrand->products()->count() > 0) {
            return redirect()->route('product-brands.index')
                ->with('error', 'Cannot delete brand with products. Please move or delete products first.');
        }

        $productBrand->update(['status' => 0]);
        $productBrand->delete();

        return redirect()->route('product-brands.index')
            ->with('success', 'Product brand deleted successfully.');
    }
}

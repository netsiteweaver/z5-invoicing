<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Concerns\HasBreadcrumbs;

class ProductCategoryController extends Controller
{
    use HasBreadcrumbs;
    /**
     * Display a listing of product categories.
     */
    public function index(Request $request)
    {
        $query = ProductCategory::with(['parent', 'children', 'products']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by parent category
        if ($request->filled('parent_id')) {
            if ($request->parent_id === 'root') {
                $query->whereNull('parent_id');
            } else {
                $query->where('parent_id', $request->parent_id);
            }
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $categories = $query->active()->ordered()->paginate(15);
        $parentCategories = ProductCategory::active()->root()->ordered()->get();

        $breadcrumbs = $this->setBreadcrumbs('product-categories.index');

        return view('product-categories.index', compact('categories', 'parentCategories') + $breadcrumbs);
    }

    /**
     * Show the form for creating a new product category.
     */
    public function create()
    {
        $parentCategories = ProductCategory::active()->root()->ordered()->get();
        
        $breadcrumbs = $this->setBreadcrumbs('product-categories.create');
        
        return view('product-categories.create', compact('parentCategories') + $breadcrumbs);
    }

    /**
     * Store a newly created product category.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'parent_id' => ['nullable', 'exists:product_categories,id'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['created_by'] = auth()->id();
        $validated['status'] = 1;

        ProductCategory::create($validated);

        return redirect()->route('product-categories.index')
            ->with('success', 'Product category created successfully.');
    }

    /**
     * Display the specified product category.
     */
    public function show(ProductCategory $productCategory)
    {
        $productCategory->load(['parent', 'children', 'products', 'createdBy', 'updatedBy']);
        
        $breadcrumbs = $this->setBreadcrumbs('product-categories.show', ['productCategory' => $productCategory]);
        
        return view('product-categories.show', compact('productCategory') + $breadcrumbs);
    }

    /**
     * Show the form for editing the specified product category.
     */
    public function edit(ProductCategory $productCategory)
    {
        $parentCategories = ProductCategory::active()->root()->ordered()->get();
        
        $breadcrumbs = $this->setBreadcrumbs('product-categories.edit', ['productCategory' => $productCategory]);
        
        return view('product-categories.edit', compact('productCategory', 'parentCategories') + $breadcrumbs);
    }

    /**
     * Update the specified product category.
     */
    public function update(Request $request, ProductCategory $productCategory)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'parent_id' => ['nullable', 'exists:product_categories,id', Rule::notIn([$productCategory->id])],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['updated_by'] = auth()->id();

        $productCategory->update($validated);

        return redirect()->route('product-categories.index')
            ->with('success', 'Product category updated successfully.');
    }

    /**
     * Remove the specified product category from storage.
     */
    public function destroy(ProductCategory $productCategory)
    {
        // Check if category has products or subcategories
        if ($productCategory->products()->count() > 0) {
            return redirect()->route('product-categories.index')
                ->with('error', 'Cannot delete category with products. Please move or delete products first.');
        }

        if ($productCategory->children()->count() > 0) {
            return redirect()->route('product-categories.index')
                ->with('error', 'Cannot delete category with subcategories. Please move or delete subcategories first.');
        }

        $productCategory->update(['status' => 0]);

        return redirect()->route('product-categories.index')
            ->with('success', 'Product category deleted successfully.');
    }
}

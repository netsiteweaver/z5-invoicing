<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductBrand;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Concerns\HasBreadcrumbs;

class ProductController extends Controller
{
    use HasBreadcrumbs;
    /**
     * Display a listing of products.
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand', 'inventory']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('stockref', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by brand
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $products = $query->active()->latest()->paginate(15);
        $categories = ProductCategory::active()->ordered()->get();
        $brands = ProductBrand::active()->ordered()->get();

        $breadcrumbs = $this->setBreadcrumbs('products.index');

        return view('products.index', compact('products', 'categories', 'brands') + $breadcrumbs);
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $categories = ProductCategory::active()->ordered()->get();
        $brands = ProductBrand::active()->ordered()->get();
        
        $breadcrumbs = $this->setBreadcrumbs('products.create');
        
        return view('products.create', compact('categories', 'brands') + $breadcrumbs);
    }

    /**
     * Store a newly created product.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'stockref' => ['required', 'string', 'max:100', 'unique:products,stockref'],
            'description' => ['nullable', 'string'],
            'category_id' => ['required', 'exists:product_categories,id'],
            'brand_id' => ['nullable', 'exists:product_brands,id'],
            'type' => ['required', 'in:product,service'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'min_selling_price' => ['nullable', 'numeric', 'min:0'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'dimensions' => ['nullable', 'string', 'max:100'],
            'sku' => ['nullable', 'string', 'max:100'],
            'barcode' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['created_by'] = auth()->id();
        $validated['status'] = 1;

        Product::create($validated);

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        $product->load(['category', 'brand', 'inventory.department', 'createdBy', 'updatedBy']);
        
        $breadcrumbs = $this->setBreadcrumbs('products.show', ['product' => $product]);
        
        return view('products.show', compact('product') + $breadcrumbs);
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        $categories = ProductCategory::active()->ordered()->get();
        $brands = ProductBrand::active()->ordered()->get();
        
        $breadcrumbs = $this->setBreadcrumbs('products.edit', ['product' => $product]);
        
        return view('products.edit', compact('product', 'categories', 'brands') + $breadcrumbs);
    }

    /**
     * Update the specified product.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'stockref' => ['required', 'string', 'max:100', Rule::unique('products', 'stockref')->ignore($product->id)],
            'description' => ['nullable', 'string'],
            'category_id' => ['required', 'exists:product_categories,id'],
            'brand_id' => ['nullable', 'exists:product_brands,id'],
            'type' => ['required', 'in:product,service'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'min_selling_price' => ['nullable', 'numeric', 'min:0'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'dimensions' => ['nullable', 'string', 'max:100'],
            'sku' => ['nullable', 'string', 'max:100'],
            'barcode' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['updated_by'] = auth()->id();

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product)
    {
        $product->update(['status' => 0]);

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }
}

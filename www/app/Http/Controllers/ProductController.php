<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductBrand;
use App\Models\Department;
use App\Models\Inventory;
use App\Models\Param;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Concerns\HasBreadcrumbs;

class ProductController extends Controller
{
    use HasBreadcrumbs;
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->middleware('permission:products.view')->only(['index', 'show']);
        $this->middleware('permission:products.create')->only(['create', 'store']);
        $this->middleware('permission:products.edit')->only(['edit', 'update']);
        $this->middleware('permission:products.delete')->only(['destroy']);
    }
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

        // Type filter removed (type is fixed to 'finished')

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
        $defaultBrandId = (int) (Param::getValue('products.default_brand_id') ?? 0);
        if (!$defaultBrandId && $brands->isNotEmpty()) {
            $defaultBrandId = (int) $brands->first()->id;
        }
        
        $breadcrumbs = $this->setBreadcrumbs('products.create');
        
        return view('products.create', compact('categories', 'brands', 'defaultBrandId') + $breadcrumbs);
    }

    /**
     * Store a newly created product.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            // 'stockref' is auto-generated
            'description' => ['nullable', 'string'],
            'category_id' => ['required', 'exists:product_categories,id'],
            'brand_id' => ['required', 'exists:product_brands,id'],
            // 'type' hidden and forced to 'finished'
            'cost_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'tax_type' => ['required', Rule::in(['standard','zero','exempt'])],
            'min_selling_price' => ['nullable', 'numeric', 'min:0'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'dimensions' => ['nullable', 'string', 'max:100'],
            'sku' => ['nullable', 'string', 'max:100'],
            'barcode' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);

        // Generate stock reference automatically
        $prefix = Param::getValue('products.prefix', 'PROD-') ?? 'PROD-';
        $padding = (int) (Param::getValue('products.padding', '6') ?? '6');
        $nextNumber = Param::incrementAndGet('products.last_number', 0);
        $padded = str_pad((string) $nextNumber, max(1, $padding), '0', STR_PAD_LEFT);
        $validated['stockref'] = $prefix . $padded;

        $validated['created_by'] = auth()->id();
        $validated['status'] = 1;
        $validated['type'] = 'finished';

        $product = Product::create($validated);

        // Auto-create zero-stock inventory entries for all active departments
        $departments = Department::active()->get();
        foreach ($departments as $department) {
            Inventory::firstOrCreate(
                [
                    'product_id' => $product->id,
                    'department_id' => $department->id,
                ],
                [
                    'current_stock' => 0,
                    'min_stock_level' => 0,
                    'max_stock_level' => null,
                    'reorder_point' => 0,
                    'cost_price' => $product->cost_price,
                    'selling_price' => $product->selling_price,
                    'created_by' => auth()->id() ?? 1,
                    'status' => 1,
                ]
            );
        }

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
            // 'stockref' is not editable from the UI
            'description' => ['nullable', 'string'],
            'category_id' => ['required', 'exists:product_categories,id'],
            'brand_id' => ['required', 'exists:product_brands,id'],
            // 'type' is hidden in the UI; keep existing value
            'cost_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'tax_type' => ['required', Rule::in(['standard','zero','exempt'])],
            'min_selling_price' => ['nullable', 'numeric', 'min:0'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'dimensions' => ['nullable', 'string', 'max:100'],
            'sku' => ['nullable', 'string', 'max:100'],
            'barcode' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['updated_by'] = auth()->id();
        $validated['type'] = 'finished';

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

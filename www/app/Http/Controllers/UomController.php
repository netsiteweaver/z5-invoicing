<?php

namespace App\Http\Controllers;

use App\Models\Uom;
use Illuminate\Http\Request;
use App\Http\Controllers\Concerns\HasBreadcrumbs;

class UomController extends Controller
{
    use HasBreadcrumbs;

    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->middleware('permission:uoms.view')->only(['index']);
        $this->middleware('permission:uoms.create')->only(['create', 'store']);
        $this->middleware('permission:uoms.edit')->only(['edit', 'update']);
        $this->middleware('permission:uoms.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = Uom::query();
        
        // Only show active UOMs by default, unless explicitly requested
        if (!$request->has('show_inactive') || $request->show_inactive != '1') {
            $query->where('status', 1);
        }
        
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('code', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%");
            });
        }

        $uoms = $query->orderBy('name')->paginate(20);

        $breadcrumbs = $this->setBreadcrumbs('uoms.index');

        return view('uoms.index', compact('uoms') + $breadcrumbs);
    }

    public function show(Uom $uom)
    {
        $breadcrumbs = $this->setBreadcrumbs('uoms.show', ['uom' => $uom]);
        
        return view('uoms.show', compact('uom') + $breadcrumbs);
    }

    public function create()
    {
        $breadcrumbs = $this->setBreadcrumbs('uoms.create');
        
        return view('uoms.create', $breadcrumbs);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:50|unique:uoms,code',
            'description' => 'nullable|string|max:255',
            'units_per_uom' => 'required|integer|min:1',
            'status' => 'nullable|in:0,1',
        ]);

        // Format the code: uppercase and replace spaces with underscores
        $validated['code'] = strtoupper(str_replace(' ', '_', $validated['code']));

        $validated['created_by'] = auth()->id();
        $validated['status'] = (int) ($validated['status'] ?? 1);

        Uom::create($validated);

        return redirect()->route('uoms.index')->with('success', 'UOM created.');
    }

    public function edit(Uom $uom)
    {
        $breadcrumbs = $this->setBreadcrumbs('uoms.edit', ['uom' => $uom]);
        
        return view('uoms.edit', compact('uom') + $breadcrumbs);
    }

    public function update(Request $request, Uom $uom)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:50|unique:uoms,code,' . $uom->id,
            'description' => 'nullable|string|max:255',
            'units_per_uom' => 'required|integer|min:1',
            'status' => 'nullable|in:0,1',
        ]);

        // Format the code: uppercase and replace spaces with underscores
        $validated['code'] = strtoupper(str_replace(' ', '_', $validated['code']));

        $validated['updated_by'] = auth()->id();
        $validated['status'] = (int) ($validated['status'] ?? 1);

        $uom->update($validated);

        return redirect()->route('uoms.index')->with('success', 'UOM updated.');
    }

    public function destroy(Uom $uom)
    {
        // Soft-deactivate instead of hard delete
        $uom->update(['status' => 0, 'updated_by' => auth()->id()]);
        return redirect()->route('uoms.index')->with('success', 'UOM deactivated.');
    }
}


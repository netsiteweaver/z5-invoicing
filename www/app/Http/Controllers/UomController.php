<?php

namespace App\Http\Controllers;

use App\Models\Uom;
use Illuminate\Http\Request;

class UomController extends Controller
{
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
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('code', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%");
            });
        }

        $uoms = $query->orderBy('name')->paginate(20);

        return view('uoms.index', compact('uoms'));
    }

    public function create()
    {
        return view('uoms.create');
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

        $validated['created_by'] = auth()->id();
        $validated['status'] = (int) ($validated['status'] ?? 1);

        Uom::create($validated);

        return redirect()->route('uoms.index')->with('success', 'UOM created.');
    }

    public function edit(Uom $uom)
    {
        return view('uoms.edit', compact('uom'));
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


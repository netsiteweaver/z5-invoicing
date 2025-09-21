<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HasBreadcrumbs;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Events\DepartmentCreated;
use App\Events\DepartmentUpdated;
use App\Events\DepartmentDeleted;

class DepartmentController extends Controller
{
    use HasBreadcrumbs;

    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->middleware('permission:departments.view')->only(['index', 'show']);
        $this->middleware('permission:departments.create')->only(['create', 'store']);
        $this->middleware('permission:departments.edit')->only(['edit', 'update']);
        $this->middleware('permission:departments.delete')->only(['destroy']);
    }

    /**
     * Display a listing of departments.
     */
    public function index(Request $request)
    {
        $query = Department::with(['manager'])
            ->ordered();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', (int) $request->status);
        } else {
            $query->where('status', 1);
        }

        if ($request->filled('manager_id')) {
            $query->where('manager_id', (int) $request->manager_id);
        }

        $departments = $query->paginate(15);
        $managers = User::active()->orderBy('name')->get();

        $breadcrumbs = $this->setBreadcrumbs('departments.index');

        return view('departments.index', compact('departments', 'managers') + $breadcrumbs);
    }

    /**
     * Show the form for creating a new department.
     */
    public function create()
    {
        $managers = User::active()->orderBy('name')->get();

        $breadcrumbs = $this->setBreadcrumbs('departments.create');

        return view('departments.create', compact('managers') + $breadcrumbs);
    }

    /**
     * Store a newly created department in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'phone_number' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'manager_id' => ['nullable', 'exists:users,id'],
            'is_main' => ['nullable', 'boolean'],
        ]);

        $validated['created_by'] = auth()->id();
        $validated['status'] = 1;
        $validated['is_main'] = (bool) ($validated['is_main'] ?? false);

        $department = Department::create($validated);

        event(new DepartmentCreated($department, auth()->user()->name ?? 'System'));

        return redirect()->route('departments.index')
            ->with('success', 'Department created successfully.');
    }

    /**
     * Display the specified department.
     */
    public function show(Department $department)
    {
        $department->load(['manager', 'createdBy', 'updatedBy']);

        $breadcrumbs = $this->setBreadcrumbs('departments.show', ['department' => $department]);

        return view('departments.show', compact('department') + $breadcrumbs);
    }

    /**
     * Show the form for editing the specified department.
     */
    public function edit(Department $department)
    {
        $managers = User::active()->orderBy('name')->get();

        $breadcrumbs = $this->setBreadcrumbs('departments.edit', ['department' => $department]);

        return view('departments.edit', compact('department', 'managers') + $breadcrumbs);
    }

    /**
     * Update the specified department in storage.
     */
    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'phone_number' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'manager_id' => ['nullable', 'exists:users,id'],
            'is_main' => ['nullable', 'boolean'],
            'status' => ['nullable', Rule::in([0,1,2])],
        ]);

        $validated['updated_by'] = auth()->id();
        if (array_key_exists('is_main', $validated)) {
            $validated['is_main'] = (bool) $validated['is_main'];
        }

        $department->update($validated);

        event(new DepartmentUpdated($department, auth()->user()->name ?? 'System'));

        return redirect()->route('departments.index')
            ->with('success', 'Department updated successfully.');
    }

    /**
     * Soft-delete the specified department (status -> 0).
     */
    public function destroy(Department $department)
    {
        $department->update(['status' => 0, 'updated_by' => auth()->id()]);

        event(new DepartmentDeleted($department, auth()->user()->name ?? 'System'));

        return redirect()->route('departments.index')
            ->with('success', 'Department deleted successfully.');
    }
}


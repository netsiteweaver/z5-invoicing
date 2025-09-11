<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        $query = Permission::query();

        // Filter by module
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $permissions = $query->orderBy('module')->orderBy('action')->paginate(50);
        
        $modules = Permission::distinct()->pluck('module')->filter()->sort();
        $actions = Permission::distinct()->pluck('action')->filter()->sort();

        return view('permissions.index', compact('permissions', 'modules', 'actions'));
    }

    public function show(Permission $permission)
    {
        $permission->load('roles');
        return view('permissions.show', compact('permission'));
    }

    public function create()
    {
        $modules = Permission::distinct()->pluck('module')->filter()->sort();
        $actions = ['view', 'create', 'edit', 'delete', 'manage', 'export', 'import'];
        
        return view('permissions.create', compact('modules', 'actions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'module' => 'required|string|max:255',
            'action' => 'required|string|max:255',
        ]);

        try {
            Permission::create([
                'name' => $request->name,
                'display_name' => $request->display_name,
                'description' => $request->description,
                'module' => $request->module,
                'action' => $request->action,
                'is_active' => true,
            ]);

            return redirect()->route('permissions.index')
                ->with('success', 'Permission created successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to create permission. Please try again.']);
        }
    }

    public function edit(Permission $permission)
    {
        $modules = Permission::distinct()->pluck('module')->filter()->sort();
        $actions = ['view', 'create', 'edit', 'delete', 'manage', 'export', 'import'];
        
        return view('permissions.edit', compact('permission', 'modules', 'actions'));
    }

    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'module' => 'required|string|max:255',
            'action' => 'required|string|max:255',
        ]);

        try {
            $permission->update([
                'name' => $request->name,
                'display_name' => $request->display_name,
                'description' => $request->description,
                'module' => $request->module,
                'action' => $request->action,
            ]);

            return redirect()->route('permissions.show', $permission)
                ->with('success', 'Permission updated successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update permission. Please try again.']);
        }
    }

    public function destroy(Permission $permission)
    {
        try {
            $permission->delete();
            return redirect()->route('permissions.index')
                ->with('success', 'Permission deleted successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete permission. Please try again.']);
        }
    }

    public function toggleStatus(Permission $permission)
    {
        $permission->update(['is_active' => !$permission->is_active]);

        $status = $permission->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Permission {$status} successfully.");
    }

    public function bulkCreate()
    {
        $modules = [
            'customers', 'products', 'product_categories', 'product_brands',
            'orders', 'sales', 'inventory', 'users', 'roles', 'permissions',
            'reports', 'dashboard', 'invoices', 'payments'
        ];
        
        $actions = ['view', 'create', 'edit', 'delete', 'manage', 'export', 'import'];
        
        return view('permissions.bulk-create', compact('modules', 'actions'));
    }

    public function bulkStore(Request $request)
    {
        $request->validate([
            'permissions' => 'required|array|min:1',
            'permissions.*.module' => 'required|string|max:255',
            'permissions.*.action' => 'required|string|max:255',
            'permissions.*.display_name' => 'required|string|max:255',
            'permissions.*.description' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $created = 0;
            $skipped = 0;

            foreach ($request->permissions as $permissionData) {
                $name = Permission::generatePermissionName($permissionData['module'], $permissionData['action']);
                
                if (!Permission::where('name', $name)->exists()) {
                    Permission::create([
                        'name' => $name,
                        'display_name' => $permissionData['display_name'],
                        'description' => $permissionData['description'],
                        'module' => $permissionData['module'],
                        'action' => $permissionData['action'],
                        'is_active' => true,
                    ]);
                    $created++;
                } else {
                    $skipped++;
                }
            }

            DB::commit();

            $message = "Bulk creation completed. Created: {$created}, Skipped: {$skipped}";
            return redirect()->route('permissions.index')->with('success', $message);
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to create permissions. Please try again.']);
        }
    }
}
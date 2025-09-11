<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->orderBy('name')->paginate(20);
        return view('roles.index', compact('roles'));
    }

    public function show(Role $role)
    {
        $role->load('permissions', 'users');
        $allPermissions = Permission::active()->orderBy('module')->orderBy('action')->get()->groupBy('module');
        return view('roles.show', compact('role', 'allPermissions'));
    }

    public function create()
    {
        $permissions = Permission::active()->orderBy('module')->orderBy('action')->get()->groupBy('module');
        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        DB::beginTransaction();
        try {
            $role = Role::create([
                'name' => $request->name,
                'display_name' => $request->display_name,
                'description' => $request->description,
                'is_active' => true,
                'is_system' => false,
            ]);

            if ($request->permissions) {
                $role->syncPermissions($request->permissions);
            }

            DB::commit();

            return redirect()->route('roles.show', $role)
                ->with('success', 'Role created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to create role. Please try again.']);
        }
    }

    public function edit(Role $role)
    {
        if ($role->is_system) {
            return redirect()->route('roles.show', $role)
                ->withErrors(['error' => 'System roles cannot be edited.']);
        }

        $permissions = Permission::active()->orderBy('module')->orderBy('action')->get()->groupBy('module');
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        if ($role->is_system) {
            return redirect()->route('roles.show', $role)
                ->withErrors(['error' => 'System roles cannot be edited.']);
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        DB::beginTransaction();
        try {
            $role->update([
                'name' => $request->name,
                'display_name' => $request->display_name,
                'description' => $request->description,
            ]);

            $role->syncPermissions($request->permissions ?? []);

            DB::commit();

            return redirect()->route('roles.show', $role)
                ->with('success', 'Role updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to update role. Please try again.']);
        }
    }

    public function destroy(Role $role)
    {
        if (!$role->canBeDeleted()) {
            return redirect()->route('roles.show', $role)
                ->withErrors(['error' => 'This role cannot be deleted. It may be a system role or is assigned to users.']);
        }

        try {
            $role->delete();
            return redirect()->route('roles.index')
                ->with('success', 'Role deleted successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete role. Please try again.']);
        }
    }

    public function toggleStatus(Role $role)
    {
        if ($role->is_system) {
            return back()->withErrors(['error' => 'System roles cannot be deactivated.']);
        }

        $role->update(['is_active' => !$role->is_active]);

        $status = $role->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Role {$status} successfully.");
    }
}
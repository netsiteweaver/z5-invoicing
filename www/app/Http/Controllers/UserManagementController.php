<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Http\Controllers\Concerns\HasBreadcrumbs;

class UserManagementController extends Controller
{
    use HasBreadcrumbs;
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        // Users
        $this->middleware('permission:users.view')->only(['index', 'show', 'apiIndex', 'apiShow']);
        $this->middleware('permission:users.create')->only(['create', 'store', 'apiStore']);
        $this->middleware('permission:users.edit')->only(['edit', 'update', 'apiUpdate']);
        $this->middleware('permission:users.delete')->only(['destroy', 'apiDestroy']);
        
        // Roles
        $this->middleware('permission:roles.view')->only(['roles']);
        $this->middleware('permission:roles.create')->only(['createRole', 'storeRole']);
        $this->middleware('permission:roles.edit')->only(['editRole', 'updateRole', 'toggleRoleStatus']);
        $this->middleware('permission:roles.delete')->only(['destroyRole']);
        
        // Permissions
        $this->middleware('permission:permissions.view')->only(['permissions']);
        $this->middleware('permission:permissions.manage')->only(['togglePermissionStatus']);
    }
    public function index(Request $request)
    {
        $query = User::with('roles');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role_id')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('role_id', $request->role_id);
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->orderBy('name')->paginate(20);
        $roles = Role::active()->orderBy('name')->get();

        $breadcrumbs = $this->setBreadcrumbs('user-management.index');

        return view('user-management.index', compact('users', 'roles') + $breadcrumbs);
    }

    public function show(User $user_management)
    {
        $user_management->load('roles.permissions');
        $allRoles = Role::active()->orderBy('name')->get();
        $allPermissions = Permission::active()->orderBy('module')->orderBy('action')->get()->groupBy('module');
        
        $breadcrumbs = $this->setBreadcrumbs('user-management.show', ['user' => $user_management]);
        
        return view('user-management.show', compact('user_management', 'allRoles', 'allPermissions') + $breadcrumbs);
    }

    public function create()
    {
        $roles = Role::active()->orderBy('name')->get();
        $departments = Department::active()->ordered()->get();
        
        $breadcrumbs = $this->setBreadcrumbs('user-management.create');
        
        return view('user-management.create', compact('roles', 'departments') + $breadcrumbs);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'user_level' => 'required|in:Normal,Admin,Root',
            'job_title' => 'nullable|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'user_level' => $request->user_level,
                'job_title' => $request->job_title,
                'department_id' => $request->department_id,
                'status' => 1,
                'created_by' => auth()->id() ?? 1,
            ]);

            if ($request->roles) {
                $user->syncRoles($request->roles);
            }

            DB::commit();

            return redirect()->route('user-management.show', $user)
                ->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to create user. Please try again.']);
        }
    }

    public function edit(User $user_management)
    {
        $roles = Role::active()->orderBy('name')->get();
        $departments = Department::active()->ordered()->get();
        $userRoles = $user_management->roles->pluck('id')->toArray();

        $breadcrumbs = $this->setBreadcrumbs('user-management.edit', ['user' => $user_management]);

        return view('user-management.edit', compact('user_management', 'roles', 'departments', 'userRoles') + $breadcrumbs);
    }

    public function update(Request $request, User $user_management)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user_management->id,
            'username' => 'required|string|max:255|unique:users,username,' . $user_management->id,
            'password' => 'nullable|confirmed',
            'user_level' => 'required|in:Normal,Admin,Root',
            'job_title' => 'nullable|string|max:255',
            'status' => 'required|in:0,1,2',
            'department_id' => 'nullable|exists:departments,id',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        DB::beginTransaction();
        try {
            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->username,
                'user_level' => $request->user_level,
                'job_title' => $request->job_title,
                'department_id' => $request->department_id,
                'status' => $request->status,
                'updated_by' => auth()->id() ?? 1,
            ];

            if ($request->password) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user_management->update($updateData);
            $user_management->syncRoles($request->roles ?? []);

            DB::commit();

            return redirect()->route('user-management.show', ['user_management' => $user_management->id])
                ->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to update user. Please try again.']);
        }
    }

    public function destroy(User $user_management)
    {
        if ($user_management->id === auth()->id()) {
            return back()->withErrors(['error' => 'You cannot delete your own account.']);
        }

        try {
            $user_management->update(['status' => 0]); // Soft delete by setting status to 0
            return redirect()->route('user-management.index')
                ->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete user. Please try again.']);
        }
    }

    // Role Management Methods
    public function roles()
    {
        $roles = Role::with('permissions', 'users')->orderBy('name')->paginate(20);
        return view('user-management.roles', compact('roles'));
    }

    public function createRole()
    {
        $permissions = Permission::active()->orderBy('module')->orderBy('action')->get()->groupBy('module');
        return view('user-management.create-role', compact('permissions'));
    }

    public function storeRole(Request $request)
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

            return redirect()->route('user-management.roles')
                ->with('success', 'Role created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to create role. Please try again.']);
        }
    }

    public function editRole(Role $role)
    {
        if ($role->is_system) {
            return redirect()->route('user-management.roles')
                ->withErrors(['error' => 'System roles cannot be edited.']);
        }

        $permissions = Permission::active()->orderBy('module')->orderBy('action')->get()->groupBy('module');
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('user-management.edit-role', compact('role', 'permissions', 'rolePermissions'));
    }

    public function showRole(Role $role)
    {
        // View-only details for predefined/system roles or any role
        $role->load(['permissions', 'users']);
        $permissions = Permission::active()->orderBy('module')->orderBy('action')->get()->groupBy('module');
        return view('user-management.show-role', compact('role', 'permissions'));
    }

    public function updateRole(Request $request, Role $role)
    {
        if ($role->is_system) {
            return redirect()->route('user-management.roles')
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

            return redirect()->route('user-management.roles')
                ->with('success', 'Role updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to update role. Please try again.']);
        }
    }

    public function destroyRole(Role $role)
    {
        if (!$role->canBeDeleted()) {
            return redirect()->route('user-management.roles')
                ->withErrors(['error' => 'This role cannot be deleted. It may be a system role or is assigned to users.']);
        }

        try {
            $role->delete();
            return redirect()->route('user-management.roles')
                ->with('success', 'Role deleted successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete role. Please try again.']);
        }
    }

    // Permission Management Methods
    public function permissions(Request $request)
    {
        $query = Permission::query();

        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $permissions = $query->orderBy('module')->orderBy('action')->paginate(50);
        
        $modules = Permission::distinct()->pluck('module')->filter()->sort();
        $actions = Permission::distinct()->pluck('action')->filter()->sort();

        return view('user-management.permissions', compact('permissions', 'modules', 'actions'));
    }

    public function togglePermissionStatus(Permission $permission)
    {
        $permission->update(['is_active' => !$permission->is_active]);

        $status = $permission->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Permission {$status} successfully.");
    }

    public function toggleRoleStatus(Role $role)
    {
        if ($role->is_system) {
            return back()->withErrors(['error' => 'System roles cannot be deactivated.']);
        }

        $role->update(['is_active' => !$role->is_active]);

        $status = $role->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Role {$status} successfully.");
    }

    // API Methods for Vue.js
    public function apiIndex(Request $request)
    {
        $query = User::with('roles');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role_id')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('role_id', $request->role_id);
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->orderBy('name')->get();

        return response()->json($users);
    }

    public function apiShow(User $user)
    {
        $user->load('roles.permissions');
        return response()->json($user);
    }

    public function apiStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'user_level' => 'required|in:Normal,Admin,Root',
            'job_title' => 'nullable|string|max:255',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'user_level' => $request->user_level,
                'job_title' => $request->job_title,
                'status' => 1,
                'created_by' => auth()->id() ?? 1,
            ]);

            if ($request->roles) {
                $user->syncRoles($request->roles);
            }

            DB::commit();

            return response()->json($user->load('roles'), 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Failed to create user.'], 500);
        }
    }

    public function apiUpdate(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'password' => 'nullable|confirmed',
            'user_level' => 'required|in:Normal,Admin,Root',
            'job_title' => 'nullable|string|max:255',
            'status' => 'required|in:0,1,2',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        DB::beginTransaction();
        try {
            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->username,
                'user_level' => $request->user_level,
                'job_title' => $request->job_title,
                'status' => $request->status,
                'updated_by' => auth()->id() ?? 1,
            ];

            if ($request->password) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);
            $user->syncRoles($request->roles ?? []);

            DB::commit();

            return response()->json($user->load('roles'));
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Failed to update user.'], 500);
        }
    }

    public function apiDestroy(User $user)
    {
        if ($user->id === (auth()->id() ?? 1)) {
            return response()->json(['error' => 'You cannot delete your own account.'], 403);
        }

        try {
            $user->update(['status' => 0]); // Soft delete by setting status to 0
            return response()->json(['message' => 'User deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete user.'], 500);
        }
    }

    public function apiRoles()
    {
        $roles = Role::active()->orderBy('name')->get();
        return response()->json($roles);
    }
}
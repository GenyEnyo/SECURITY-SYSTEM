<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('permissions')->orderBy('name')->get();

        return view('role-permission.role.index', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'role_name' => 'required|string|max:255|unique:roles,name',
        ]);

        $role = Role::create(['name' => $data['role_name'], 'guard_name' => 'web']);

        activity('access')
            ->causedBy($request->user())
            ->performedOn($role)
            ->withProperties(['attributes' => ['name' => $role->name]])
            ->log('role created');

        return redirect()->route('roles.index')->with('status', 'Role created');
    }

    public function update(Request $request, Role $role)
    {
        // Super-Admin is matched by name in Gate::before, so it keeps its name.
        abort_if($role->name === 'Super-Admin', 403, 'The Super-Admin role cannot be renamed.');

        $data = $request->validate([
            'role_name' => ['required', 'string', 'max:255', Rule::unique('roles', 'name')->ignore($role->id)],
        ]);

        $oldName = $role->name;
        $role->update(['name' => $data['role_name']]);

        activity('access')
            ->causedBy($request->user())
            ->performedOn($role)
            ->withProperties(['old' => ['name' => $oldName], 'attributes' => ['name' => $role->name]])
            ->log('role renamed');

        return redirect()->route('roles.index')->with('status', 'Role updated');
    }

    public function addPermissionToRole(Role $role)
    {
        $permissions = Permission::orderBy('name')->get();
        $rolePermissions = $role->permissions->pluck('id')->all();

        return view('role-permission.role.add-permissions', compact('role', 'permissions', 'rolePermissions'));
    }

    public function givePermissionToRole(Request $request, Role $role)
    {
        $data = $request->validate([
            'permission'   => 'nullable|array',
            'permission.*' => 'string|exists:permissions,name',
        ]);

        $oldPermissions = $role->permissions->pluck('name')->all();
        $role->syncPermissions($data['permission'] ?? []);

        activity('access')
            ->causedBy($request->user())
            ->performedOn($role)
            ->withProperties(['old' => ['permissions' => $oldPermissions], 'attributes' => ['permissions' => $data['permission'] ?? []]])
            ->log('permissions changed');

        return redirect()->route('roles.index')->with('status', "Permissions updated for {$role->name}");
    }
}

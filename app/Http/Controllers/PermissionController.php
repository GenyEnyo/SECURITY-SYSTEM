<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::orderBy('name')->get();

        return view('role-permission.permission.index', compact('permissions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'permission_name' => 'required|string|max:255|unique:permissions,name',
        ]);

        $permission = Permission::create(['name' => $data['permission_name'], 'guard_name' => 'web']);

        activity('access')
            ->causedBy($request->user())
            ->performedOn($permission)
            ->withProperties(['attributes' => ['name' => $permission->name]])
            ->log('permission created');

        return redirect()->route('permissions.index')->with('status', 'Permission created');
    }

    public function update(Request $request, Permission $permission)
    {
        $data = $request->validate([
            'permission_name' => ['required', 'string', 'max:255', Rule::unique('permissions', 'name')->ignore($permission->id)],
        ]);

        $oldName = $permission->name;
        $permission->update(['name' => $data['permission_name']]);

        activity('access')
            ->causedBy($request->user())
            ->performedOn($permission)
            ->withProperties(['old' => ['name' => $oldName], 'attributes' => ['name' => $permission->name]])
            ->log('permission renamed');

        return redirect()->route('permissions.index')->with('status', 'Permission updated');
    }
}

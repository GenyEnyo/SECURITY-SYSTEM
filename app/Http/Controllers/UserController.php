<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->orderBy('name')->get();

        return view('role-permission.user.index', compact('users'));
    }

    public function addRoleToUser(User $user)
    {
        $roles = Role::orderBy('name')->get();
        $userRoles = $user->roles->pluck('name')->all();

        return view('role-permission.user.add-role', compact('user', 'roles', 'userRoles'));
    }

    public function giveRoleToUser(Request $request, User $user)
    {
        $data = $request->validate([
            'role'   => 'nullable|array',
            'role.*' => 'string|exists:roles,name',
        ]);

        $roles = $data['role'] ?? [];

        // Only a Super-Admin may grant or revoke the Super-Admin role.
        $touchesSuperAdmin = in_array('Super-Admin', $roles, true) !== $user->hasRole('Super-Admin');
        abort_if($touchesSuperAdmin && ! $request->user()->hasRole('Super-Admin'), 403, 'Only a Super-Admin can change the Super-Admin role.');

        $oldRoles = $user->getRoleNames()->all();
        $user->syncRoles($roles);

        activity('access')
            ->causedBy($request->user())
            ->performedOn($user)
            ->withProperties(['old' => ['roles' => $oldRoles], 'attributes' => ['roles' => $roles]])
            ->log('roles changed');

        return redirect()->route('users.index')->with('status', "Roles updated for {$user->name}");
    }
}

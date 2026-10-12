<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['roles', 'location'])->orderBy('name')->get();

        return view('role-permission.user.index', compact('users'));
    }

    public function addRoleToUser(User $user)
    {
        $roles = Role::orderBy('name')->get();
        $userRoles = $user->roles->pluck('name')->all();

        // User administration is not limited to the admin's own location.
        $locations = Location::withoutGlobalScope('user_location')->orderBy('name')->get();
        $departments = User::whereNotNull('department')->distinct()->orderBy('department')->pluck('department')
            ->push(config('security.supervisor_department'))->unique()->values();
        $sections = User::whereNotNull('section')->distinct()->orderBy('section')->pluck('section')
            ->push(config('security.supervisor_section'))->unique()->values();

        return view('role-permission.user.add-role', compact('user', 'roles', 'userRoles', 'locations', 'departments', 'sections'));
    }

    public function giveRoleToUser(Request $request, User $user)
    {
        $data = $request->validate([
            'role'   => 'nullable|array',
            'role.*' => 'string|exists:roles,name',
            'location_id' => 'nullable|exists:locations,id',
            'department'  => 'nullable|string|max:255',
            'section'     => 'nullable|string|max:255',
        ]);

        $roles = $data['role'] ?? [];

        // Only a Super-Admin may grant or revoke the Super-Admin role.
        $touchesSuperAdmin = in_array('Super-Admin', $roles, true) !== $user->hasRole('Super-Admin');
        abort_if($touchesSuperAdmin && ! $request->user()->hasRole('Super-Admin'), 403, 'Only a Super-Admin can change the Super-Admin role.');

        $oldRoles = $user->getRoleNames()->all();
        $user->syncRoles($roles);
        $user->update(Arr::only($data, ['location_id', 'department', 'section']));

        activity('access')
            ->causedBy($request->user())
            ->performedOn($user)
            ->withProperties(['old' => ['roles' => $oldRoles], 'attributes' => ['roles' => $roles]])
            ->log('roles changed');

        return redirect()->route('users.index')->with('status', "Details updated for {$user->name}");
    }
}

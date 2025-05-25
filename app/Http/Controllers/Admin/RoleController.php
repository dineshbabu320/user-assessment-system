<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;
use App\Models\User;
use App\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $currentCid = auth()->user()->cid;

        $users = User::where('cid', '!=', $currentCid)
            ->whereHas('roles', function ($q) {
                $q->where('roles.id', '!=', 1);
            })
            ->with('roles')
            ->paginate(10);

        $roles = Role::all();
        return view('admin.rolecontrol', compact('users', 'roles'));
    }

    public function assignRole(Request $request)
    {
        $request->validate([
            'cid' => 'required|exists:users,cid',
            'role_ids' => 'nullable|array',
            'role_ids.*' => 'integer|exists:roles,id',
        ]);

        $user = User::where('cid', $request->cid)->firstOrFail();

        $roles = $request->role_ids ?? [];

        // If empty, revert to Student
        if (empty($roles)) {
            $user->roles()->sync([1]);
            ActivityLogger::log("All roles removed for {$user->name} ({$user->cid}), revert to Student by " . auth()->user()->name);
            return redirect()->route('admin.roles.index')->with('success', "All roles removed. User now Student.");
        }

        // If SUP present, only assign SUP
        $supIds = [3];
        $supSelected = array_intersect($roles, $supIds);
        if (!empty($supSelected)) {
            $user->roles()->sync($supSelected);
        } else {
            $user->roles()->sync($roles);
        }

        ActivityLogger::log("Updated roles for {$user->name} ({$user->cid}) by " . auth()->user()->name);

        return redirect()->route('admin.roles.index')->with('success', "Roles updated for {$user->name} (CID: {$user->cid}).");
    }

    public function adjustRoles(Request $request)
    {
        $request->validate([
            'cid' => 'required|exists:users,cid',
            'role_ids' => 'nullable|array',
            'role_ids.*' => 'integer|exists:roles,id',
        ]);

        $user = User::where('cid', $request->cid)->firstOrFail();

        $roles = $request->role_ids ?? [];

        // If empty, revert to Student
        if (empty($roles)) {
            $user->roles()->sync([1]);
            ActivityLogger::log("All roles removed for {$user->name} ({$user->cid}), revert to Student by " . auth()->user()->name);
            return redirect()->route('admin.roles.index')->with('success', "All roles removed. User now Student.");
        }

        // If SUP present, only assign SUP
        $supIds = [3];
        $supSelected = array_intersect($roles, $supIds);
        if (!empty($supSelected)) {
            $user->roles()->sync($supSelected);
        } else {
            $user->roles()->sync($roles);
        }

        ActivityLogger::log("Adjusted roles for {$user->name} ({$user->cid}) by " . auth()->user()->name);

        return redirect()->route('admin.roles.index')->with('success', "Roles adjusted for {$user->name} (CID: {$user->cid}).");
    }
}

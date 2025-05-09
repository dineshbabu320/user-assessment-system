<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;
use App\Models\User;

class RoleController extends Controller
{
    public function index()
    {
        // Fetch users with role_id 2 (Subdivision Directors) or 3 (Admins)
        $users = User::whereIn('role_id', [2, 3])->paginate(10);

        return view('admin.rolecontrol', compact('users'));
    }

    public function assignRole(Request $request)
    {
        $request->validate([
            'cid' => 'required|exists:users,cid', 
            'role_id' => 'required|in:2,3', 
        ]);

        $user = User::where('cid', $request->cid)->firstOrFail(); 
        $user->role_id = $request->role_id; 
        $user->save();

        //Log the role assignment activity
        if($request->role_id==2) {
            ActivityLogger::log("Added role Subdivision Director to {$request->cid} by " . auth()->user()->name);
        }
        else {
            ActivityLogger::log("Added role Admin to {$request->cid} by " . auth()->user()->name);
        }

        return redirect()->route('admin.roles.index')->with('success', "Role updated for {$user->name} (CID: {$user->cid}).");
    }
}

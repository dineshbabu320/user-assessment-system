<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\AdminSubdivisionNotification;
use App\Http\Controllers\Controller;
use App\Models\User;

class AdminNotificationController extends Controller
{
    public function edit()
    {
        $admin = auth()->user();
        $subdivisions = User::select('subdivision_id', 'subdivision_name')
            ->distinct()
            ->where('subdivision_id', '!=', 'NIL')
            ->get();

        $selected = $admin->subdivisionNotifications->pluck('subdivision_id')->toArray();

        return view('admin.subdivision_notifications', compact('subdivisions', 'selected'));
    }

    public function update(Request $request)
    {
        $admin = auth()->user();
        $subdivisions = $request->input('subdivisions', []);

        $admin->subdivisionNotifications()->delete();

        foreach ($subdivisions as $subdivisionId) {
            AdminSubdivisionNotification::create([
                'admin_cid' => $admin->cid,
                'subdivision_id' => $subdivisionId,
            ]);
        }

        return redirect()->route('admin.notifications.edit')->with('success', 'Notification preferences updated.');
    }
}

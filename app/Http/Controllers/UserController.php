<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function updateMailPreferences(Request $request)
    {
        $request->validate([
            'notification_email' => 'nullable|email',
        ]);

        $user = auth()->user();
        $user->notification_email = $request->notification_email;
        $user->save();

        return back()->with('success', 'Notification email updated!');
    }
}

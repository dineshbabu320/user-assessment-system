<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    public static function log($action)
    {
        ActivityLog::create([
            'action' => $action,
            'user_cid' => Auth::check() ? Auth::user()->cid : 'System',
        ]);
    }
}
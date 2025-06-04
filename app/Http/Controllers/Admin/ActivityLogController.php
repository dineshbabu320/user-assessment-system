<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::query();

        $from = $request->input('from');
        $to = $request->input('to');

        // Convert mm/dd/yyyy to yyyy-mm-dd if present
        if ($from) {
            $from = \Carbon\Carbon::createFromFormat('m/d/Y', $from)->format('Y-m-d');
        }
        if ($to) {
            $to = \Carbon\Carbon::createFromFormat('m/d/Y', $to)->format('Y-m-d');
        }

        if ($from && $to) {
            $query->whereBetween('created_at', [
                "$from 00:00:00",
                "$to 23:59:59"
            ]);
        } elseif ($from) {
            $query->where('created_at', '>=', $from . ' 00:00:00');
        } elseif ($to) {
            $query->where('created_at', '<=', $to . ' 23:59:59');
        }

        $logs = $query->latest()->paginate(10);

        return view('admin.auditlogs', compact('logs'));
    }
}

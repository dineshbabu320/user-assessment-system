<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class StudentHistoryController extends Controller
{
    public function index($cid)
    {
        $student = User::where('cid', $cid)
            ->where(function ($query) {
                if (!auth()->user()->isAdmin()) {
                    $query->where('subdivision_id', auth()->user()->subdivision_id);
                }
            })
            ->with(['tokens', 'attempts.exam'])
            ->firstOrFail();

        // Custom query parameters
        $tokens = $student->tokens()
            ->orderBy('issued_at', 'desc')
            ->paginate(5, ['*'], 'tokens_page');

        $attempts = $student->attempts()
            ->with('exam')
            ->orderBy('attempted_at', 'desc')
            ->paginate(5, ['*'], 'attempts_page');

        return view('students.student-history', compact('student', 'tokens', 'attempts'));
    }
}

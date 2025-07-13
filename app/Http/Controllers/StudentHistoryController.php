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
                if (!auth()->user()->isAdmin() && !auth()->user()->isExaminer()) {
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

    public function search(Request $request)
    {
        $term = $request->input('q');
        $excludeId = auth()->id();

        $users = User::where('cid', '!=', $excludeId)
            ->when(!auth()->user()->canSearchAllStudents(), function ($query) {
                // not admin, filter by own subdivision
                $query->where('subdivision_id', auth()->user()->subdivision_id);
            })
            ->where(function($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('cid', 'like', "%{$term}%");
            })
            ->limit(10)
            ->get(['name', 'cid']);

        return response()->json($users);
    }
}

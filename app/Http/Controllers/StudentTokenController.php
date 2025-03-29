<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Token;
use App\Models\Exam;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class StudentTokenController extends Controller
{
    // Student Search Page
    public function search(Request $request)
    {
        $query = $request->input('query');

        // Fetch students based on name and limit by subdivision if the user is a Subdivision Director
        $students = User::where('name', 'LIKE', "%{$query}%")
            ->when(Auth::user()->role->name === 'Subdivision Director', function ($q) {
                $q->where('subdivision_id', Auth::user()->subdivision_id);
            })
            ->get(['cid', 'name', 'email', 'subdivision_id']);

        return view('students.search', compact('students'));
    }

    // Issue Exam Token
    public function issueToken(Request $request)
    {
        $request->validate([
            'user_cid' => 'required|exists:users,cid',
            'exam_id' => 'required|exists:exams,id',
        ]);

        $user = User::where('cid', $request->user_cid)->first();
        $exam = Exam::findOrFail($request->exam_id);
        
        // Ensure subdivision directors can issue only within their subdivision
        if ($user->subdivision_id !== Auth::user()->subdivision_id) {
            return redirect()->back()->with('error', 'You can only issue tokens within your subdivision.');
        }

        // Create a token with a 14-day expiry
        Token::create([
            'id' => Str::uuid(),
            'user_cid' => $user->cid,
            'exam_id' => $exam->id,
            'issued_at' => now(),
            'expires_at' => now()->addDays(14),
        ]);

        return redirect()->back()->with('success', "Token issued for {$user->name} (CID: {$user->cid}) for the {$exam->name} exam.");
    }
}

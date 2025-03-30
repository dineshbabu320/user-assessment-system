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
        ]);

        $user = User::where('cid', $request->user_cid)->first();
        
        $tokenId = $request->input('token_id');
        $examId = substr($tokenId, strrpos($tokenId, '-') + 1); // Get the part after the last '-'

        // Ensure the exam_id exists in the exams table
        $exam = Exam::where('name', $examId)->first();
        if (!$exam) {
            return redirect()->back()->with('error', "Invalid exam ID '{$examId}' in the token.");
        }
        
        // Ensure subdivision directors can issue only within their subdivision
        if ($user->subdivision_id !== Auth::user()->subdivision_id) {
            return redirect()->back()->with('error', 'You can only issue tokens within your subdivision.');
        }

        // Create a token with a 14-day expiry
        Token::create([
            'id' => $tokenId,
            'user_cid' => $user->cid,
            'exam_id' => $exam->id,
            'issued_at' => now(),
            'expires_at' => now()->addDays(14),
        ]);

        return redirect()->back()->with('success', "Token issued for {$user->name} (CID: {$user->cid}) for the {$exam->name} exam.");
    }
}

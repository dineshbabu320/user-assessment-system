<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Token;
use App\Models\Exam;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\TokenIssuedMail;
use App\Models\Attempt;
use App\Helpers\ActivityLogger;

class StudentTokenController extends Controller
{
    // Student Search Page
    public function index()
    {
        $students = User::select('cid', 'name', 'subdivision_name') 
            ->when(Auth::user()->isSubdivisionStaff(), function ($q) {
                $q->where('subdivision_id', Auth::user()->subdivision_id);
            })
            ->paginate(10);

        return view('students.search', compact('students'));
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $students = collect();

        if ($query) {
            $students = User::query()
                ->when(Auth::user()->isSubdivisionStaff(), function ($q) {
                    $q->where('subdivision_id', Auth::user()->subdivision_id);
                })
                ->when(is_numeric($query), function($q) use ($query) {
                    $q->where('cid', $query);
                }, function($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%");
                })
                ->paginate(10);
        }

        return view('students.search', compact('students'));
    }

    // Issue Exam Token
    public function issueToken(Request $request)
    {
        $request->validate([
            'user_cid' => 'required|exists:users,cid',
            'token_id' => 'required|string',
        ]);

        $user = User::where('cid', $request->user_cid)->first();
        
        // Check for cooldown period in the attempts table
        $cooldown = Attempt::where('user_cid', $request->user_cid)
            ->where('status', 'failed') 
            ->whereNotNull('cooldown_until') 
            ->orderBy('cooldown_until', 'desc')
            ->first();

        // if ($cooldown && now()->lessThan($cooldown->cooldown_until)) {
        //     return redirect()->back()->with('error', "Token cannot be issued. Cooldown period ends on {$cooldown->cooldown_until->format('Y-m-d')}.");
        // }

        $tokenId = $request->input('token_id');
        $examId = substr($tokenId, strrpos($tokenId, '-') + 1);

        $activeToken = Token::where('id', '=',$tokenId)
                    ->where('status', '=','active')
                    ->where('user_cid', $request->user_cid)
                    ->first();

        if($activeToken) {
            return redirect()->back()->with('error','The user already has an active token for the exam');
        }

        $exam = Exam::where('name', $examId)->first();
        if (!$exam) {
            return redirect()->back()->with('error', "Invalid exam ID '{$examId}' in the token.");
        }

        // Ensure subdivision directors can issue only within their subdivision
        if (Auth::user()->isSubdivisionStaff()) {
            if ($user->subdivision_id !== Auth::user()->subdivision_id) {
                return redirect()->back()->with('error', 'You can only issue tokens within your subdivision.');
            }
        }

        // IDOR Protection: Prevent non-admins from issuing admin-level tokens
        if (!Auth::user()->isAdmin()) {
            $allowedExams = ['S1', 'S2', 'S3', 'C1'];
            if (!in_array($examId, $allowedExams)) {
                return redirect()->back()->with('error', 'You are not authorized to issue tokens for this exam.');
            }
        }

        $token = Token::create([
            'id' => $tokenId,
            'user_cid' => $user->cid,
            'exam_id' => $exam->id,
            'issued_at' => now(),
            'expires_at' => now()->addDays(14),
        ]);

        Mail::to($user->email)->queue(new TokenIssuedMail($token));

        ActivityLogger::log("Issued token '{$tokenId}' for {$user->name} ({$user->cid}) by " . auth()->user()->name . " (" . auth()->user()->cid . ")");

        return redirect()->back()->with('success', "Token issued for {$user->name} (CID: {$user->cid}) for the {$exam->name} exam.");
    }

    public function revokeToken(Request $request, $tokenId)
    {
        $token = Token::findOrFail($tokenId);

        // Check if the token is already revoked
        if ($token->status === 'revoked') {
            return redirect()->back()->with('error', 'This token is already revoked.');
        }

        $token->update(['status' => 'revoked']);

        ActivityLogger::log("Revoked token '{$token->id}' for user ({$token->user_cid}) by " . auth()->user()->name . " (" . auth()->user()->cid . ")");

        return redirect()->back()->with('success', "Token ID {$token->id} has been successfully revoked.");
    }
}

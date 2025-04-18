<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Token;
use App\Models\Exam;
use App\Models\RatingUpgradeRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\RatingUpgradeNotification;
use App\Mail\TokenIssuedMail;
use App\Models\Attempt; // Add this import if not already present

class StudentTokenController extends Controller
{
    // Student Search Page
    public function search(Request $request)
    {
        $query = $request->input('query');

        // Fetch students based on name and limit by subdivision if the user is a Subdivision Director
        $students = User::select('cid', 'name', 'email', 'subdivision_id') // Specify the columns to retrieve
            ->where('name', 'LIKE', "%{$query}%")
            ->when(Auth::user()->role->name === 'Subdivision Director', function ($q) {
                $q->where('subdivision_id', Auth::user()->subdivision_id);
            })
            ->paginate(10); // Paginate results (10 per page)

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
            ->where('status', 'failed') // Assuming 'failed' indicates a failed attempt
            ->whereNotNull('cooldown_until') // Ensure cooldown_until is set
            ->orderBy('cooldown_until', 'desc') // Get the latest cooldown
            ->first();

        // if ($cooldown && now()->lessThan($cooldown->cooldown_until)) {
        //     return redirect()->back()->with('error', "Token cannot be issued. Cooldown period ends on {$cooldown->cooldown_until->format('Y-m-d')}.");
        // }

        $tokenId = $request->input('token_id');
        $examId = substr($tokenId, strrpos($tokenId, '-') + 1); // Get the part after the last '-'

        // Ensure the exam_id exists in the exams table
        $exam = Exam::where('name', $examId)->first();
        if (!$exam) {
            return redirect()->back()->with('error', "Invalid exam ID '{$examId}' in the token.");
        }

        // Ensure subdivision directors can issue only within their subdivision
        if ($user->roleAboveStudent() && $user->isSubdivisionDirector()) {
            if ($user->subdivision_id !== Auth::user()->subdivision_id) {
                return redirect()->back()->with('error', 'You can only issue tokens within your subdivision.');
            }
        }

        $token = Token::create([
            'id' => $tokenId,
            'user_cid' => $user->cid,
            'exam_id' => $exam->id,
            'issued_at' => now(),
            'expires_at' => now()->addDays(14),
        ]);

        // If the token is REC type, create a rating upgrade request
        if (str_starts_with($request->token_id, 'REC')) {
            $rating = substr($request->token_id, 4); // Extract the rating name after 'REC-'

            RatingUpgradeRequest::create([
                'user_cid' => $request->user_cid,
                'token_id' => $request->token_id,
                'rating' => $rating, // Store the rating
            ]);

            // Notify admin via email
            Mail::to("hvdineshbabu@gmail.com")->queue(new RatingUpgradeNotification($token));
        }
        else {
            Mail::to("hvdineshbabu@gmail.com")->queue(new TokenIssuedMail($token));
        }

        return redirect()->back()->with('success', "Token issued for {$user->name} (CID: {$user->cid}) for the {$exam->name} exam.");
    }
}

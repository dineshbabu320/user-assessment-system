<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Exam;
use App\Models\Attempt;

class HasExamToken
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Check if the route contains an attemptId
        $attemptId = $request->route('attemptId');
        if ($attemptId) {
            $attempt = Attempt::where('id', $attemptId)
                ->where('user_cid', $user->cid)
                ->first();

            if (!$attempt) {
                return redirect()->route('dashboard')->with('error', 'The requested attempt does not exist.');
            }

            $exam = $attempt->exam;

            if (!$exam) {
                return redirect()->route('dashboard')->with('error', 'The requested exam does not exist.');
            }

            $hasValidToken = $user->tokens()
                ->where('user_cid', auth()->user()->cid)
                ->where('id', 'READY-' . $exam->name)
                ->where('expires_at', '>', now())
                ->where('status', 'active')
                ->exists();

            if (!$hasValidToken) {
                return redirect()->route('dashboard')->with('error', 'You are not authorized for this exam.');
            }

            return $next($request);
        }

        // If no attemptId, fallback to the existing examName logic
        $examName = $request->route('examName');

        // Fetch the exam by name
        $exam = Exam::where('name', $examName)->first();

        if (!$exam) {
            return redirect()->route('dashboard')->with('error', 'The requested exam does not exist.');
        }

        // Check if the user has a valid token for the exam
        $hasValidToken = $user->tokens()
            ->where('user_cid', auth()->user()->cid)
            ->where('id', 'READY-' . $examName)
            ->where('expires_at', '>', now())
            ->where('status', 'active')
            ->exists();

        if (!$hasValidToken) {
            return redirect()->route('dashboard')->with('error', 'You are not authorized for this exam.');
        }

        return $next($request);
    }
}

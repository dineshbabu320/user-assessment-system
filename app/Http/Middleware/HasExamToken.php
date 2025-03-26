<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HasExamToken
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        $examId = $request->route('exam');

        // Check if the user has a token for the requested exam
        if (!$user->tokens()->where('exam_id', $examId)->exists()) {
            return redirect()->route('dashboard')->with('error', 'You are not authorized for this exam.');
        }

        return $next($request);
    }
}

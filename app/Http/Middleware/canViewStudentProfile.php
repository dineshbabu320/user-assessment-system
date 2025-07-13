<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CanViewStudentProfile
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('front')->with('error', 'You must be logged in to access this page.');
        }
        
        $user = Auth::user();
        
        // Only allow admin, examiner, subdivision staff, and mentors
        if (!$user->isAdmin() && !$user->isExaminer() && !$user->isSubdivisionStaff() && !$user->isMentor()) {
            abort(403, 'Unauthorized access');
        }
        
        return $next($request);
    }
}
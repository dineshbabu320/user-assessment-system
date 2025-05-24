<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SubdivisionStaff
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('front')->with('error', 'You must be logged in to access this page.');
        }
        if (!Auth::user()->isSubdivisionStaff() && !Auth::user()->isAdmin()) {
            abort(401);
        }
        return $next($request);
    }
}

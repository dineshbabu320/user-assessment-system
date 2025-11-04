<?php

namespace App\Http\Middleware;

use App\Http\Controllers\OAuthController;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class RefreshOAuthToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user && $user->refresh_token) {
            if (!$user->token_expires_at || 
                $user->token_expires_at->subMinutes(10)->isPast()) {
                
                try {
                    $provider = new OAuthController();
                    $newToken = $provider->getAccessToken('refresh_token', [
                        'refresh_token' => $user->refresh_token
                    ]);

                    // Update user's tokens
                    $user->access_token = $newToken->getToken();
                    $user->refresh_token = $newToken->getRefreshToken() ?: $user->refresh_token;
                    $user->token_expires_at = $newToken->getExpires() ? 
                        Carbon::createFromTimestamp($newToken->getExpires()) : null;
                    $user->save();

                } catch (\Exception $e) {
                    auth()->logout();
                    return redirect()->route('front')->withErrors('Your session has expired. Please log in again.');
                }
            }
        }

        return $next($request);
    }
}

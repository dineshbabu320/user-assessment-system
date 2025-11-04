<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\OAuthController;
use App\Models\User;
use Illuminate\Http\Request;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;

/**
 * This controller handles authenticating users for the application and
 * redirecting them to your home screen. The controller uses a trait
 * to conveniently provide its functionality to your applications.
 */
class LoginController extends Controller
{

    protected $provider;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->provider = new OAuthController();
    }

    /**
     * Login the user
     *
     * @param  \Illuminate\Http\Request  $request  request to proccess
     * @return mixed
     */
    public function login(Request $request)
    {
        if (auth()->check()) {
            return redirect()->intended(route('dashboard'));
        }

        if (!$request->has('code') || !$request->has('state')) {
            $authorizationUrl = $this->provider->getAuthorizationUrl([
                'required_scopes' => implode(' ', config('oauth.scopes')),
                'scope' => implode(' ', config('oauth.scopes')),
            ]);
            $request->session()->put('oauthstate', $this->provider->getState());

            return redirect()->away($authorizationUrl);
        } elseif ($request->input('state') !== session()->pull('oauthstate')) {
            return redirect()->route('front')->withError('Something went wrong, please try again (state mismatch).');
        } else {
            return $this->verifyLogin($request);
        }
    }

    /**
     * Verify the login of the user's request before proceeding
     *
     * @param  \Illuminate\Http\Request  $request  request to proccess
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function verifyLogin(Request $request)
    {
        try {
            $accessToken = $this->provider->getAccessToken('authorization_code', [
                'code' => $request->input('code'),
            ]);
        } catch (IdentityProviderException $e) {
            return redirect()->route('front')->withErrors('Authentication error: ' . $e->getMessage());
        }

        $resourceOwner = json_decode(json_encode($this->provider->getResourceOwner($accessToken)->toArray()));
        if ($resourceOwner->data->vatsim->division->id !== 'SEA' || $resourceOwner->data->vatsim->rating->id<1) {
            return redirect()->route('front')->withErrors('You are not authorized to access this system.');
        }

        try {
            $account = $this->completeLogin($resourceOwner->data, $accessToken);
        } catch (\Exception $e) {
            return redirect()->route('front')->withErrors($e->getMessage());
        }

        // Login the user and don't remember the session forever
        auth()->login($account, false);

        return redirect()->intended(route('dashboard'))->withSuccess('Login Successful');
    }

    /**
     * Complete the login by creating or updating the existing account and last login timestamp
     *
     * @param  mixed  $token
     * @return \App\Models\User User's account data
     */
    protected function completeLogin($data, $token)
    {
        $account = User::firstOrNew(['cid' => $data->cid]);

        $account->name = $data->personal->name_full;
        $account->email = $data->personal->email;
        $account->atc_rating = $data->vatsim->rating->id;

        $account->access_token = $token->getToken();
        $account->refresh_token = $token->getRefreshToken();
        $account->token_expires_at = $token->getExpires() ?
            \Carbon\Carbon::createFromTimestamp($token->getExpires()) : null;

        // For VATGOV
        if ($data->vatsim->rating->id == 12) {
            $account->subdivision_id = "NIL";
            $account->subdivision_name = "NIL";
            $account->roles()->syncWithoutDetaching([3]);
        } else {
            // Check if subdivision data exists
            if (!isset($data->vatsim->subdivision) || !isset($data->vatsim->subdivision->id) || empty($data->vatsim->subdivision->id)) {
                throw new \Exception('You are not assigned to a subdivision yet. Please contact staff members to have your subdivision assignment completed before accessing the system.');
            }

            $account->subdivision_id = $data->vatsim->subdivision->id;
            $account->subdivision_name = $data->vatsim->subdivision->name;
        }

        if (!$account->exists) {
            $account->save();
            $account->roles()->syncWithoutDetaching([1]);
        }

        $account->save();

        return $account;
    }

    /**
     * Refresh the user's access token if needed
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    protected function refreshTokenIfNeeded($user)
    {
        if (
            !$user->token_expires_at ||
            $user->token_expires_at->subMinutes(10)->isPast()
        ) {

            if (!$user->refresh_token) {
                return false;
            }

            try {
                $newToken = $this->provider->getAccessToken('refresh_token', [
                    'refresh_token' => $user->refresh_token
                ]);

                // Update user's tokens
                $user->access_token = $newToken->getToken();
                $user->refresh_token = $newToken->getRefreshToken() ?: $user->refresh_token;
                $user->token_expires_at = $newToken->getExpires() ?
                    \Carbon\Carbon::createFromTimestamp($newToken->getExpires()) : null;
                $user->save();

                return true;
            } catch (\Exception $e) {
                return false;
            }
        }

        return true; // Token is still valid
    }

    /**
     * Log out he user and redirect to front page
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        auth()->logout();

        return redirect(route('front'))->withSuccess('You have been successfully logged out');
    }
}
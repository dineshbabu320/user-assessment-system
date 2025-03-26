<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Vatsim\OAuth2\Client\Provider\Vatsim;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    protected $vatsim;

    public function __construct()
    {
        $this->vatsim = new Vatsim([
            'clientId'      => env('VATSIM_CLIENT_ID'),
            'clientSecret'  => env('VATSIM_CLIENT_SECRET'),
            'redirectUri'   => env('VATSIM_REDIRECT_URI'),
            'domain'        => env('VATSIM_ENV') === 'dev' 
                                ? 'https://auth-dev.vatsim.net' 
                                : 'https://auth.vatsim.net',
        ]);
    }

    /**
     * Redirect the user to VATSIM for authentication.
     */
    public function redirectToVatsim(Request $request)
    {
        $authorizationUrl = $this->vatsim->getAuthorizationUrl();
        $request->session()->put('oauth2state', $this->vatsim->getState());
        return redirect($authorizationUrl);
    }

    /**
     * Handle OAuth callback from VATSIM.
     */
    public function handleVatsimCallback(Request $request)
    {
        // Validate state to prevent CSRF attacks
        if ($request->input('state') !== session()->pull('oauth2state')) {
            return redirect('/')->with('error', 'Invalid OAuth state.');
        }

        // Try to get an access token
        try {
            $token = $this->vatsim->getAccessToken('authorization_code', [
                'code' => $request->code,
            ]);
        } catch (\Exception $e) {
            return redirect('/')->with('error', 'Failed to authenticate.');
        }

        // Fetch user details from VATSIM
        try {
            $vatsimUser = $this->vatsim->getResourceOwner($token);
            $userData = $vatsimUser->toArray();
        } catch (\Exception $e) {
            return redirect('/')->with('error', 'Failed to fetch user details.');
        }

        // Extract required fields
        $vatsim_id = $userData['data']['cid'];
        $name = $userData['data']['personal']['name_full'];
        $email = $userData['data']['personal']['email'];
        $atc_rating = $userData['data']['vatsim']['rating']['id'];
        $division = $userData['data']['vatsim']['division']['id'];
        $subdivision = $userData['data']['vatsim']['subdivision']['id'] ?? null;

        // Check if the user belongs to the allowed division
        if ($division !== env('VATSIM_ALLOWED_DIVISION')) {
            return redirect('/login')->with('error', 'You must belong to the correct division.');
        }

        // Create or update the user in the database
        $user = User::updateOrCreate(
            ['vatsim_id' => $vatsim_id],
            [
                'name' => $name,
                'email' => $email,
                'role_id' => 1, // Assign "basic role" initially
                'subdivision_id' => $subdivision, // Store subdivision, not division
            ]
        );

        // Log in the user
        Auth::login($user, true); // "true" enables persistent login

        return redirect()->route('dashboard');
    }

    /**
     * Logout the user.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        return redirect('/login');
    }
}

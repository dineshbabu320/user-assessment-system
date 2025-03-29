<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attempt;
use App\Models\Token;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Fetch user rating from the stored data
        $ratings = [
            1  => ['short' => 'OBS', 'long' => 'Pilot/Observer'],
            2  => ['short' => 'S1', 'long' => 'Tower Trainee'],
            3  => ['short' => 'S2', 'long' => 'Tower Controller'],
            4  => ['short' => 'S3', 'long' => 'TMA Controller'],
            5  => ['short' => 'C1', 'long' => 'Enroute Controller'],
            6  => ['short' => 'C2', 'long' => 'Senior Controller'],
            7  => ['short' => 'C3', 'long' => 'Senior Controller'],
            8  => ['short' => 'I1', 'long' => 'Instructor'],
            9  => ['short' => 'I2', 'long' => 'Senior Instructor'],
            10 => ['short' => 'I3', 'long' => 'Senior Instructor'],
            11 => ['short' => 'SUP', 'long' => 'Supervisor'],
            12 => ['short' => 'ADM', 'long' => 'Administrator']
        ];

        $user_rating = $ratings[$user->atc_rating] ?? ['short' => 'UNK', 'long' => 'Unknown'];

        // Get user's token status
        $active_token = Token::where('user_cid', $user->cid)->where('expires_at', '>', now())->first();

        // Get user's exam attempts
        $exam_attempts = Attempt::where('user_cid', $user->cid)->orderBy('attempted_at', 'desc')->get();

        return view('dashboard', compact('user_rating', 'active_token', 'exam_attempts'));
    }
}

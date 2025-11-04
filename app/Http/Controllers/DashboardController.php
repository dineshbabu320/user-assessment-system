<?php

namespace App\Http\Controllers;

use App\Models\Attempt;
use App\Models\Token;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Fetch user rating from the stored data
        $ratings = [
            1 => ['short' => 'OBS', 'long' => 'Pilot/Observer'],
            2 => ['short' => 'S1', 'long' => 'Tower Trainee'],
            3 => ['short' => 'S2', 'long' => 'Tower Controller'],
            4 => ['short' => 'S3', 'long' => 'TMA Controller'],
            5 => ['short' => 'C1', 'long' => 'Enroute Controller'],
            6 => ['short' => 'C2', 'long' => 'Senior Controller'],
            7 => ['short' => 'C3', 'long' => 'Senior Controller'],
            8 => ['short' => 'I1', 'long' => 'Instructor'],
            9 => ['short' => 'I2', 'long' => 'Senior Instructor'],
            10 => ['short' => 'I3', 'long' => 'Senior Instructor'],
            11 => ['short' => 'SUP', 'long' => 'Supervisor'],
            12 => ['short' => 'ADM', 'long' => 'Administrator']
        ];

        $user_rating = $ratings[$user->atc_rating] ?? ['short' => 'UNK', 'long' => 'Unknown'];

        $subdivisionName = $user->subdivision_name;
        $subdivisionID = $user->subdivision_id;
        
        $active_token = Token::where('user_cid', $user->cid)
            ->where('expires_at', '>', now())
            ->where('id', 'LIKE', 'READY-%')
            ->where('status', 'active')
            ->first();

        $exam_attempts = Attempt::where('user_cid', $user->cid)
            ->orderBy('attempted_at', 'desc')
            ->paginate(5);

        // Recent attempts logic (last 30 attempts, paginated)
        $recentAttempts = collect();

        if ($user->isAdmin()) {
            $allAttempts = Attempt::with(['user', 'exam'])
                ->where('user_cid', '!=', $user->cid)
                ->orderBy('attempted_at', 'desc')
                ->take(30)
                ->get();
                
            $currentPage = request()->get('recent_attempts_page', 1);
            $perPage = 5;
            $currentItems = $allAttempts->slice(($currentPage - 1) * $perPage, $perPage);
            
            $recentAttempts = new LengthAwarePaginator(
                $currentItems,
                $allAttempts->count(),
                $perPage,
                $currentPage,
                [
                    'path' => request()->url(),
                    'pageName' => 'recent_attempts_page'
                ]
            );
        } elseif ($user->isSubdivisionStaff() || $user->isMentor()) {
            $allAttempts = Attempt::with(['user', 'exam'])
                ->whereHas('user', function ($q) use ($user) {
                    $q->where('subdivision_id', $user->subdivision_id);
                })
                ->where('user_cid', '!=', $user->cid)
                ->orderBy('attempted_at', 'desc')
                ->take(30)
                ->get();
                
            $currentPage = request()->get('recent_attempts_page', 1);
            $perPage = 5;
            $currentItems = $allAttempts->slice(($currentPage - 1) * $perPage, $perPage);
            
            $recentAttempts = new LengthAwarePaginator(
                $currentItems,
                $allAttempts->count(),
                $perPage,
                $currentPage,
                [
                    'path' => request()->url(),
                    'pageName' => 'recent_attempts_page'
                ]
            );
        }

        return view('dashboard', compact(
            'user_rating',
            'active_token',
            'exam_attempts',
            'subdivisionName',
            'subdivisionID',
            'recentAttempts'
        ));
    }
}

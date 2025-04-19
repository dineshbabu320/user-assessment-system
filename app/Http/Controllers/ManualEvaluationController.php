<?php

namespace App\Http\Controllers;

use App\Models\Response;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\ExamPassedNotification;
use Illuminate\Http\Request;

class ManualEvaluationController extends Controller
{
    // List students with pending evaluations
    public function index()
    {
        $students = User::whereHas('responses', function ($query) {
            $query->where('status', 'pending')
                ->when(!auth()->user()->isAdmin(), function ($query) {
                    $query->whereHas('user', function ($subQuery) {
                        $subQuery->where('subdivision_id', auth()->user()->subdivision_id);
                    });
                });
        })->withCount(['responses as pending_evaluations' => function ($query) {
            $query->where('status', 'pending');
        }])->paginate(10);

        return view('students.manual-evaluation-list', compact('students'));
    }

    // Show pending evaluations for a specific student
    public function show($cid)
    {
        $student = User::where('cid', $cid)
            ->when(!auth()->user()->isAdmin(), function ($query) {
                $query->where('subdivision_id', auth()->user()->subdivision_id);
            })
            ->firstOrFail();

        $responses = Response::where('user_cid', $cid)
            ->where('status', 'pending')
            ->with('question')
            ->get();

        return view('students.manual-evaluation', compact('student', 'responses'));
    }

    // Evaluate all responses for a specific student
    public function evaluateAll(Request $request, $cid)
    {
        $request->validate([
            'responses' => 'required|array',
            'responses.*.status' => 'required|in:correct,incorrect',
            'responses.*.comments' => 'nullable|string',
        ]);

        foreach ($request->responses as $responseId => $data) {
            $response = Response::findOrFail($responseId);
            $response->update([
                'status' => $data['status'],
                'comments' => $data['comments'] ?? null,
                'evaluated_by' => auth()->user()->cid,
                'evaluated_at' => now(),
            ]);
        }

        // Recalculate the attempt's status and score
        $attempt = $response->attempt;
        $responses = $attempt->responses;

        $totalScore = $responses->where('status', 'correct')->sum(function ($response) {
            return $response->question->points;
        });

        $maxScore = $attempt->exam->questions->sum('points');
        $hasPendingEvaluation = $responses->where('status', 'pending')->isNotEmpty();

        $status = $hasPendingEvaluation
            ? 'waiting_for_manual_evaluation'
            : (($totalScore / $maxScore) >= 0.8 ? 'passed' : 'failed');

        $attempt->update([
            'status' => $status,
            'score' => $totalScore,
            'cooldown_until' => $status === 'failed' ? now()->addDays($attempt->attempt_number * 7) : null,
        ]);

        if ($status === 'passed') {
            $user = auth()->user();
            $exam = $attempt->exam;
    
            // Get admin and subdivision directors
            $adminEmail = 'js.hoo@vat-sea.com';
            $subdivisionDirectors = User::where('role_id', 2)
                ->where('subdivision_id', $user->subdivision_id)
                ->pluck('email')
                ->toArray();
    
            Mail::to($subdivisionDirectors)->bcc($adminEmail)->queue(new ExamPassedNotification($user, $exam, $totalScore));
        }

        return redirect()->route('student.manualEvaluation')->with('success', 'All evaluations submitted successfully.');
    }
}

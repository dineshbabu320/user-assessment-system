<?php

namespace App\Http\Controllers;

use App\Models\Response;
use App\Models\User;
use App\Models\Attempt;
use Illuminate\Support\Facades\Mail;
use App\Mail\ExamPassedNotification;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;
use Parsedown;

class ManualEvaluationController extends Controller
{
    private const adminEmail = 'js.hoo@vat-sea.com';

    // List students with pending evaluations
    public function index()
    {
        $students = User::whereHas('responses', function ($query) {
            $query->where('status', 'pending');
        })->withCount(['responses as pending_evaluations' => function ($query) {
            $query->where('status', 'pending');
        }])->paginate(10);

        return view('students.manual-evaluation-list', compact('students'));
    }

    // Show pending evaluations for a specific student
    public function show($cid)
    {
        $student = User::where('cid', $cid)->firstOrFail();

        $responses = Response::where('user_cid', $cid)
            ->where('status', 'pending')
            ->with('question')
            ->get();

        $parsedown = new Parsedown();
        foreach ($responses as $response) {
            $response->question->parsed_text = $parsedown->text($response->question->question_text);
        }

        return view('students.manual-evaluation', compact('student', 'responses'));
    }

    // Evaluate all responses for a specific student
    public function evaluateAll(Request $request, $cid)
    {
        $request->validate([
            'responses' => 'required|array',
            'responses.*.status' => 'required|in:correct,incorrect',
            'responses.*.points' => 'required|numeric|min:0',
            'responses.*.comments' => 'nullable|string',
        ]);

        foreach ($request->responses as $responseId => $data) {
            $response = Response::findOrFail($responseId);
            $response->update([
                'status' => $data['status'],
                'points_awarded' => $data['points'], // Store custom points
                'comments' => $data['comments'] ?? null,
                'evaluated_by' => auth()->user()->cid,
                'evaluated_at' => now(),
            ]);
        }

        // Recalculate the attempt's status and score
        $attempt = $response->attempt;
        $responses = $attempt->responses;

        $totalScore = $responses->sum('points_awarded'); // Sum custom points
        $totalScore += $attempt->score;

        $maxScore = $responses->sum(function ($response) {
            return $response->question->points;
        });

        $hasPendingEvaluation = $responses->where('status', 'pending')->isNotEmpty();

        $status = $hasPendingEvaluation
            ? 'waiting_for_manual_evaluation'
            : (($totalScore / $maxScore) >= 0.8 ? 'passed' : 'failed');

        $attempt->update([
            'status' => $status,
            'score' => $totalScore,
            'cooldown_until' => $status === 'failed' ? now()->addDays(3) : null,
        ]);

        if ($status === 'passed') {
            $user = auth()->user();
            $exam = $attempt->exam;

            $subdivisionDirectors = User::whereHas('roles', function ($query) {
                    $query->where('id', 2);
                })
                ->where('subdivision_id', $user->subdivision_id)
                ->pluck('email')
                ->toArray();
    
            Mail::to($subdivisionDirectors)->bcc(self::adminEmail)->queue(new ExamPassedNotification($user, $exam, $totalScore));
        }

        // Log the action
        ActivityLogger::log("Submitted evaluations for student CID '{$cid}' by " . auth()->user()->name);

        return redirect()->route('student.manualEvaluation')->with('success', 'All evaluations submitted successfully.');
    }

    public function manualEvaluate($attemptId)
    {
        $attempt = Attempt::with(['responses.question', 'exam'])->findOrFail($attemptId);

        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $parsedown = new Parsedown();
        foreach ($attempt->responses as $response) {
            $response->question->parsed_text = $parsedown->text($response->question->question_text);
        }

        return view('admin.manual-evaluation-admin', compact('attempt'));
    }

    public function submitManualEvaluation(Request $request, $attemptId)
    {
        $request->validate([
            'responses' => 'required|array',
            'responses.*.status' => 'required|in:correct,incorrect',
            'responses.*.points' => 'required|numeric|min:0',
            'responses.*.comments' => 'nullable|string',
        ]);

        $attempt = Attempt::with(['responses.question'])->findOrFail($attemptId);

        foreach ($request->responses as $responseId => $data) {
            $response = $attempt->responses->find($responseId);
            if ($response) {
                $response->update([
                    'status' => $data['status'],
                    'points_awarded' => $data['points'], // Store custom points
                    'comments' => $data['comments'] ?? null,
                    'evaluated_by' => auth()->user()->cid,
                    'evaluated_at' => now(),
                ]);
            }
        }

        // Recalculate the attempt's score and status
        $totalScore = $attempt->responses->sum('points_awarded'); // Sum custom points

        $maxScore = $attempt->responses->sum(function ($response) {
            return $response->question->points;
        });

        $status = ($totalScore / $maxScore) >= 0.8 ? 'passed' : 'failed';

        $attempt->update([
            'score' => $totalScore,
            'status' => $status,
        ]);

        if ($status === 'passed') {
            $user = auth()->user();
            $exam = $attempt->exam;

            $subdivisionDirectors = User::whereHas('roles', function ($query) {
                    $query->where('id', 2);
                })
                ->where('subdivision_id', $user->subdivision_id)
                ->pluck('email')
                ->toArray();
            
            Mail::to($subdivisionDirectors)->bcc(self::adminEmail)->queue(new ExamPassedNotification($user, $exam, $totalScore));
        }

        // Log the action
        ActivityLogger::log("Manually evaluated attempt ID '{$attemptId}' for student with CID ({$attempt->user_cid}) by admin " . auth()->user()->name);

        return redirect()->route('student.studentHistory', $attempt->user_cid)->with('success', 'Manual evaluation completed successfully.');
    }
}

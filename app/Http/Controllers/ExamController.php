<?php
namespace App\Http\Controllers;

use App\Models\Token;
use App\Models\Response;
use App\Models\Attempt;
use App\Models\Exam;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    /**
     * Show instructions before starting the exam.
     */
    public function instructions($examName)
    {
        $token = Token::where('user_cid', auth()->user()->cid)
            ->where('id', 'READY-' . $examName)
            ->where('expires_at', '>', now())
            ->firstOrFail();

        return view('exam.instructions', ['examName' => $examName]);
    }

    /**
     * Display the exam form.
     */
    public function takeExam($examName)
    {
        $token = Token::where('user_cid', auth()->user()->cid)
            ->where('id', 'READY-' . $examName)
            ->where('expires_at', '>', now())
            ->firstOrFail();

        $exam = $token->exam()->with('questions.answers')->firstOrFail();

        return view('exam.take', ['exam' => $exam]);
    }

    /**
     * Submit the exam and evaluate responses.
     */
    public function submitExam(Request $request, $examName)
    {
        $token = Token::where('user_cid', auth()->user()->cid)
            ->where('id', 'READY-' . $examName)
            ->where('expires_at', '>', now())
            ->firstOrFail();

        $exam = $token->exam()->with('questions.answers')->firstOrFail();

        // Check previous attempts
        $previousAttempts = Attempt::where('user_cid', auth()->user()->cid)
            ->where('exam_id', $exam->id)
            ->orderBy('attempt_number', 'desc')
            ->get();

        $attemptNumber = $previousAttempts->count() + 1;

        // Create the attempt first
        $attempt = Attempt::create([
            'user_cid' => auth()->user()->cid,
            'exam_id' => $exam->id,
            'attempt_number' => $attemptNumber,
            'attempted_at' => now(),
            'status' => 'waiting_for_manual_evaluation', // Temporary status
            'cooldown_until' => null,
        ]);

        $attemptId = $attempt->id; // Get the attempt ID
        \Log::channel('custom')->info('Attempt ID', [$attemptId]);

        $totalScore = 0;
        $maxScore = $exam->questions->count(); // Each question is worth 1 point
        $hasPendingEvaluation = false;

        // Process responses
        foreach ($exam->questions as $question) {
            $userResponse = $request->input("answers.{$question->id}");

            if ($question->question_type === 'mcq') {
                if (is_null($userResponse)) {
                    // Question not answered
                    Response::create([
                        'user_cid' => auth()->user()->cid,
                        'attempt_id' => $attemptId,
                        'question_id' => $question->id,
                        'response_text' => null,
                        'status' => 'incorrect',
                    ]);
                    continue;
                }

                $correctAnswers = $question->answers->where('is_correct', true)->pluck('id')->map(fn($id) => (string) $id)->sort()->values();
                $userAnswers = collect($userResponse)->map(fn($answer) => (string) $answer)->sort()->values();
                $isCorrect = $correctAnswers->toArray() === $userAnswers->toArray();

                if ($isCorrect) {
                    $totalScore++;
                }

                Response::create([
                    'user_cid' => auth()->user()->cid,
                    'attempt_id' => $attemptId,
                    'question_id' => $question->id,
                    'response_text' => implode(',', $userAnswers->toArray()),
                    'status' => $isCorrect ? 'correct' : 'incorrect',
                ]);
            } elseif ($question->question_type === 'single') {
                if (is_null($userResponse)) {
                    // Question not answered
                    Response::create([
                        'user_cid' => auth()->user()->cid,
                        'attempt_id' => $attemptId,
                        'question_id' => $question->id,
                        'response_text' => null,
                        'status' => 'incorrect',
                    ]);
                    continue;
                }

                $isCorrect = $question->answers->where('is_correct', true)->pluck('id')->map(fn($id) => (string) $id)->contains((string) $userResponse);

                if ($isCorrect) {
                    $totalScore++;
                }

                Response::create([
                    'user_cid' => auth()->user()->cid,
                    'attempt_id' => $attemptId,
                    'question_id' => $question->id,
                    'response_text' => (string) $userResponse,
                    'status' => $isCorrect ? 'correct' : 'incorrect',
                ]);
            } elseif ($question->question_type === 'text') {
                $hasPendingEvaluation = true; // Mark as pending evaluation
                Response::create([
                    'user_cid' => auth()->user()->cid,
                    'attempt_id' => $attemptId,
                    'question_id' => $question->id,
                    'response_text' => $userResponse,
                    'status' => 'pending',
                ]);
            }
        }

        // Determine the final status of the attempt
        if ($hasPendingEvaluation) {
            $status = 'waiting_for_manual_evaluation';
        } else {
            $passRate = 0.8; // 80%
            $status = ($totalScore / $maxScore) >= $passRate ? 'passed' : 'failed';
        }
        \Log::channel('custom')->info('Exam Score Check\n',[$totalScore]);

        // Update the attempt with the final status and score
        $attempt->update([
            'status' => $status,
            'cooldown_until' => $status === 'failed' ? now()->addDays($attemptNumber * 7) : null,
            'score' => $totalScore,
        ]);

        return redirect()->route('exam.review', $attemptId)->with('success', 'Exam submitted successfully.');
    }

    /**
     * Display the exam review page.
     */
    public function reviewExam($attemptId)
    {
        \Log::channel('custom')->info('Review Exam called', ['attemptId' => $attemptId]);

        // Fetch the attempt
        $attempt = Attempt::where('id', $attemptId)
            ->where('user_cid', auth()->user()->cid)
            ->first();

        if (!$attempt) {
            return redirect()->route('dashboard')->with('error', 'The requested attempt does not exist.');
        }

        // Fetch the associated exam
        $exam = Exam::where('id', $attempt->exam_id)->with('questions.answers')->first();

        // Fetch responses for the specific attempt
        $responses = Response::where('user_cid', auth()->user()->cid)
            ->whereIn('question_id', $exam->questions->pluck('id'))
            ->where('attempt_id', $attemptId)
            ->get()
            ->keyBy('question_id');

        // Calculate total score
        $maxScore = $exam->questions->count() * 1; // Assuming each question is worth 1 point
        $totalScore = $responses->where('status', 'correct')->count();

        return view('exam.review', [
            'exam' => $exam,
            'responses' => $responses,
            'attempt' => $attempt,
            'totalScore' => $totalScore,
            'maxScore' => $maxScore,
        ]);
    }
}

<?php
namespace App\Http\Controllers;

use App\Models\Token;
use App\Models\Response;
use App\Models\Attempt;
use App\Models\Exam;
use App\Models\User;
use Illuminate\Http\Request;
use App\Mail\ExamPassedNotification;
use Illuminate\Support\Facades\Mail;
use App\Helpers\ActivityLogger;

class ExamController extends Controller
{
    /**
     * My Exams section
     */
    public function myExams()
    {
        $user = auth()->user();

        // Fetch active tokens for the user
        $active_tokens = Token::where('user_cid', $user->cid)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->where('id','LIKE','READY-%')
            ->with('exam') // Assuming a relationship exists between Token and Exam
            ->get();

        return view('exam.my-exams', compact('active_tokens'));
    }

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

        $questionLimit = $exam->question_limit ?? $exam->questions->count();
        $questions = $exam->questions->shuffle()->take($questionLimit);

        // Store the limited questions in the session
        session(['limited_questions' => $questions->pluck('id')->toArray()]);
        \Log::channel('custom')->info('Limited questions set in session', session('limited_questions'));

        return view('exam.take', [
            'exam' => $exam,
            'questions' => $questions, // Pass the limited and shuffled questions
        ]);
    }

    /**
     * Submit the exam and evaluate responses.
     */
    public function submitExam(Request $request, $examName)
    {
        $token = Token::where('user_cid', auth()->user()->cid)
            ->where('id', 'READY-' . $examName)
            ->where('expires_at', '>', now())
            ->where('status', 'active')
            ->firstOrFail();

        $exam = $token->exam()->with('questions.answers')->firstOrFail();

        // Fetch the limited questions from the session
        $limitedQuestionIds = session('limited_questions', []);

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
        $maxScore = $exam->questions->whereIn('id', $limitedQuestionIds)->sum('points'); // Sum of points for limited questions
        $hasPendingEvaluation = false;

        // Process responses for limited questions only
        foreach ($exam->questions->whereIn('id', $limitedQuestionIds) as $question) {
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
                    $totalScore += $question->points; // Add points for correct answer
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
                    $totalScore += $question->points; // Add points for correct answer
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

        // Mark the token as used
        $token->update(['status' => 'used']);

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

        // Log the action
        ActivityLogger::log("Submitted exam '{$examName}' by " . auth()->user()->name);

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

        $attendedQuestionIds = $responses->keys();
        $questions = $exam->questions->whereIn('id',$attendedQuestionIds);

        $maxScore = $exam->questions->whereIn('id', $attendedQuestionIds)->sum('points');
        $totalScore = $attempt->score;

        return view('exam.review', [
            'exam' => $exam,
            'responses' => $responses,
            'attempt' => $attempt,
            'totalScore' => $totalScore,
            'maxScore' => $maxScore,
            'questions' => $questions,
        ]);
    }

    public function examSettings()
    {
        $exams = Exam::with('questions')->get(); // Fetch all exams with their questions
        return view('admin.admin-settings', compact('exams'));
    }

    public function updateExamSettings(Request $request)
    {
        $request->validate([
            'question_limits' => 'required|array',
            'question_limits.*' => 'integer|min:1',
        ]);

        foreach ($request->question_limits as $examId => $limit) {
            $exam = Exam::findOrFail($examId);
            $maxQuestions = $exam->questions()->count();

            if ($limit > $maxQuestions) {
                return redirect()->back()->withErrors("The limit for '{$exam->name}' cannot exceed the total number of questions ({$maxQuestions}).");
            }

            $exam->update(['question_limit' => $limit]);
        }

        return redirect()->route('admin.settings')->with('success', 'Exam settings updated successfully.');
    }
}

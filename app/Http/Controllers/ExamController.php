<?php
namespace App\Http\Controllers;

use App\Models\Token;
use App\Models\Response;
use App\Models\Attempt;
use App\Models\Exam;
use App\Models\User;
use Illuminate\Http\Request;
use Parsedown;
use App\Mail\ExamPassedNotification;
use App\Mail\ExamFailedNotification;
use Illuminate\Support\Facades\Mail;
use App\Helpers\ActivityLogger;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ExamController extends Controller
{
    // Constants
    private const PASS_RATE = 0.8; // 80%
    private const ADMIN_EMAIL = 'js.hoo@vat-sea.com';
    private const TOKEN_PREFIX = 'READY-';

    /**
     * Process an MCQ question response
     *
     * @param string $attemptId The attempt ID
     * @param object $question The question object
     * @param mixed $userResponse The user's response
     * @return array [score, isCorrect] The score earned and whether the answer was correct
     */
    private function processMCQResponse(string $attemptId, $question, $userResponse): array
    {
        if (is_null($userResponse)) {
            // Question not answered
            $this->createResponse($attemptId, $question->id, null, 'incorrect');
            return [0, false];
        }

        $correctAnswers = $question->answers->where('is_correct', true)->pluck('id')->map(fn($id) => (string) $id)->sort()->values();
        $userAnswers = collect($userResponse)->map(fn($answer) => (string) $answer)->sort()->values();
        $isCorrect = $correctAnswers->toArray() === $userAnswers->toArray();

        $score = $isCorrect ? $question->points : 0;

        $this->createResponse(
            $attemptId,
            $question->id,
            implode(',', $userAnswers->toArray()),
            $isCorrect ? 'correct' : 'incorrect'
        );

        return [$score, $isCorrect];
    }

    /**
     * Process a single-choice question response
     *
     * @param string $attemptId The attempt ID
     * @param object $question The question object
     * @param mixed $userResponse The user's response
     * @return array [score, isCorrect] The score earned and whether the answer was correct
     */
    private function processSingleResponse(string $attemptId, $question, $userResponse): array
    {
        if (is_null($userResponse)) {
            // Question not answered
            $this->createResponse($attemptId, $question->id, null, 'incorrect');
            return [0, false];
        }

        $isCorrect = $question->answers->where('is_correct', true)->pluck('id')->map(fn($id) => (string) $id)->contains((string) $userResponse);

        $score = $isCorrect ? $question->points : 0;

        $this->createResponse(
            $attemptId,
            $question->id,
            (string) $userResponse,
            $isCorrect ? 'correct' : 'incorrect'
        );

        return [$score, $isCorrect];
    }

    /**
     * Process a text question response
     *
     * @param string $attemptId The attempt ID
     * @param object $question The question object
     * @param mixed $userResponse The user's response
     * @return array [hasPendingEvaluation] Whether the response needs manual evaluation
     */
    private function processTextResponse(string $attemptId, $question, $userResponse): array
    {
        $this->createResponse($attemptId, $question->id, $userResponse, 'pending');
        return [true]; // Has pending evaluation
    }

    /**
     * Validate and retrieve an active exam token for the current user
     *
     * @param string $examName The name of the exam
     * @param bool $checkStatus Whether to check the token status
     * @return Token The validated token
     * @throws ModelNotFoundException If the token is not found or invalid
     */
    private function validateExamToken(string $examName, bool $checkStatus = true): Token
    {
        $query = Token::where('user_cid', auth()->user()->cid)
            ->where('id', self::TOKEN_PREFIX . $examName)
            ->where('expires_at', '>', now());

        if ($checkStatus) {
            $query->where('status', 'active');
        }

        return $query->firstOrFail();
    }

    /**
     * Create a response record for a question
     *
     * @param string $attemptId The attempt ID
     * @param int $questionId The question ID
     * @param mixed $responseText The response text
     * @param string $status The response status
     * @return Response The created response
     */
    private function createResponse(string $attemptId, int $questionId, $responseText, string $status): Response
    {
        return Response::create([
            'user_cid' => auth()->user()->cid,
            'attempt_id' => $attemptId,
            'question_id' => $questionId,
            'response_text' => $responseText,
            'status' => $status,
        ]);
    }
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
            ->where('id','LIKE', self::TOKEN_PREFIX . '%')
            ->with('exam')
            ->get();

        return view('exam.my-exams', compact('active_tokens'));
    }

    /**
     * Show instructions before starting the exam.
     */
    public function instructions($examName)
    {
        $token = $this->validateExamToken($examName, false);
        return view('exam.instructions', ['examName' => $examName]);
    }

    /**
     * Display the exam form.
     */
    public function takeExam(Request $request, $examName)
    {
        $token = $this->validateExamToken($examName);
        $exam = $token->exam()->with('questions')->firstOrFail();

        $attemptId = $request->query('attempt');
        $attempt = Attempt::where('id', $attemptId)
            ->where('user_cid', auth()->user()->cid)
            ->where('exam_id', $exam->id)
            ->where('status', 'in_progress')
            ->first();

        if (!$attempt || !$attempt->question_ids) {
            return redirect()->route('exam.instructions', $examName)->with('error', 'Please start the exam first.');
        }

        $selectedQuestions = $exam->questions->whereIn('id', $attempt->question_ids);

        $parsedown = new Parsedown();
        foreach ($selectedQuestions as $question) {
            $question->parsed_text = $parsedown->text($question->question_text);
        }

        return view('exam.take', [
            'exam' => $exam,
            'questions' => $selectedQuestions,
            'attemptId' => $attempt->id,
        ]);
    }

    /**
     * Submit the exam and evaluate responses.
     */
    public function submitExam(Request $request, $examName)
    {
        $token = $this->validateExamToken($examName);
        $exam = $token->exam()->with('questions.answers')->firstOrFail();

        $attemptId = $request->input('attempt_id');
        $attempt = Attempt::where('id', $attemptId)
            ->where('user_cid', auth()->user()->cid)
            ->where('exam_id', $exam->id)
            ->where('status', 'in_progress')
            ->firstOrFail();

        $limitedQuestionIds = $attempt->question_ids ?? [];

        $totalScore = 0;
        $maxScore = $exam->questions->whereIn('id', $limitedQuestionIds)->sum('points'); // Sum of points for limited questions
        $hasPendingEvaluation = false;

        // Process responses for limited questions only
        foreach ($exam->questions->whereIn('id', $limitedQuestionIds) as $question) {
            $userResponse = $request->input("answers.{$question->id}");

            if ($question->question_type === 'mcq') {
                [$score, $_] = $this->processMCQResponse($attemptId, $question, $userResponse);
                $totalScore += $score;
            } elseif ($question->question_type === 'single') {
                [$score, $_] = $this->processSingleResponse($attemptId, $question, $userResponse);
                $totalScore += $score;
            } elseif ($question->question_type === 'text') {
                [$hasPendingEvaluation] = $this->processTextResponse($attemptId, $question, $userResponse);
            }
        }

        // Determine the final status of the attempt
        if ($hasPendingEvaluation) {
            $status = 'waiting_for_manual_evaluation';
        } else {
            $status = ($totalScore / $maxScore) >= self::PASS_RATE ? 'passed' : 'failed';
        }
        \Log::channel('custom')->info('Exam Score Check\n',[$totalScore]);

        // Update the attempt with the final status and score
        $attempt->update([
            'status' => $status,
            'cooldown_until' => $status === 'failed' ? now()->addDays(3) : null,
            'score' => $totalScore,
        ]);

        // Mark the token as used
        $token->update(['status' => 'used']);

        if ($status === 'passed') {
            $user = auth()->user();
            $exam = $attempt->exam;

            $subdivisionDirectors = User::where('subdivision_id', $user->subdivision_id)
                ->whereHas('roles', function ($q) {
                    $q->where('id', 2);
                })
                ->pluck('email')
                ->toArray();

            $percentage=$maxScore > 0 ? round(($totalScore / $maxScore) * 100, 1) : 0;

            $attempt_number = $attempt->attempt_number;
            
            Mail::to($subdivisionDirectors)->bcc(self::ADMIN_EMAIL)->queue(new ExamPassedNotification($user, $exam, $percentage, $attempt_number));
        }

        if ($status === 'failed') {
            $user = auth()->user();
            $exam = $attempt->exam;

            $subdivisionDirectors = User::where('subdivision_id', $user->subdivision_id)
                ->whereHas('roles', function ($q) {
                    $q->where('id', 2);
                })
                ->pluck('email')
                ->toArray();

            $percentage=$maxScore > 0 ? round(($totalScore / $maxScore) * 100, 1) : 0;

            $attempt_number = $attempt->attempt_number;
            
            Mail::to($subdivisionDirectors)->bcc(self::ADMIN_EMAIL)->queue(new ExamFailedNotification($user, $exam, $percentage, $attempt_number));
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

        $attempt = Attempt::where('id', $attemptId)
            ->with(['exam.questions.answers', 'responses', 'user'])
            ->first();

        if (!$attempt) {
            return redirect()->route('dashboard')->with('error', 'The requested attempt does not exist.');
        }

        $currentUser = auth()->user();

        $isOwner = $attempt->user_cid === $currentUser->cid;
        $isSubdivisionStaff = $currentUser->isSubdivisionStaff() && $currentUser->subdivision_id === $attempt->user->subdivision_id;
        $isAdmin = $currentUser->isAdmin();

        if (!($isOwner || $isSubdivisionStaff || $isAdmin)) {
            return redirect()->route('dashboard')->with('error', 'You are not authorized to view this review.');
        }

        $exam = $attempt->exam;
        $responses = $attempt->responses->keyBy('question_id');
        $attendedQuestionIds = $responses->keys();
        $questions = $exam->questions->whereIn('id', $attendedQuestionIds);

        $parsedown = new Parsedown();
        foreach ($questions as $question) {
            $question->parsed_text = $parsedown->text($question->question_text);
        }

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
        $exams = Exam::with('questions')->get();
        return view('admin.admin-settings', compact('exams'));
    }

    public function updateExamSettings(Request $request)
    {
        $request->validate([
            'question_limits' => 'required|array',
            'question_limits.*' => 'integer|min:1',
            'category_ratios' => 'required|array',
            'category_ratios.*' => 'array',
            'category_ratios.*.*' => 'nullable|integer|min:0',
        ]);

        foreach ($request->question_limits as $examId => $limit) {
            $exam = Exam::findOrFail($examId);
            $maxQuestions = $exam->questions()->count();

            if ($limit > $maxQuestions) {
                return redirect()->back()->withErrors("The limit for '{$exam->name}' cannot exceed the total number of questions ({$maxQuestions}).");
            }

            $exam->update(['question_limit' => $limit]);
        }

        foreach ($request->category_ratios as $examId => $ratios) {
            $exam = Exam::findOrFail($examId);

            // Validate that the sum of category ratios does not exceed the question limit
            $totalRatio = array_sum($ratios);
            $questionLimit = $exam->question_limit ?? $exam->questions()->count();

            if ($totalRatio > $questionLimit) {
                return redirect()->back()->withErrors("The sum of category ratios for '{$exam->name}' cannot exceed the total question limit ({$questionLimit}).");
            }

            $exam->update(['category_ratios' => $ratios]);
        }

        ActivityLogger::log("Settings updated by ".auth()->user()->name);

        return redirect()->route('admin.settings')->with('success', 'Exam settings updated successfully.');
    }

    public function startExam(Request $request, $examName)
    {
        $token = $this->validateExamToken($examName);
        $exam = $token->exam()->with('questions')->firstOrFail();

        // Check for existing in-progress attempt
        $attempt = Attempt::where('user_cid', auth()->user()->cid)
            ->where('exam_id', $exam->id)
            ->where('status', 'in_progress')
            ->first();

        if (!$attempt) {
            // Randomize questions ONCE
            $categoryRatios = $exam->category_ratios ?? [];
            $selectedQuestions = collect();
            foreach ($categoryRatios as $category => $count) {
                $categoryQuestions = $exam->questions->where('category', $category);
                $selectedQuestions = $selectedQuestions->merge($categoryQuestions->shuffle()->take($count));
            }
            $selectedQuestions = $selectedQuestions->shuffle();

            // Attempt number: only count completed attempts
            $attemptNumber = Attempt::where('user_cid', auth()->user()->cid)
                ->where('exam_id', $exam->id)
                ->where('status', '!=', 'in_progress')
                ->count() + 1;

            $attempt = Attempt::create([
                'user_cid' => auth()->user()->cid,
                'exam_id' => $exam->id,
                'attempt_number' => $attemptNumber,
                'attempted_at' => now(),
                'status' => 'in_progress',
                'cooldown_until' => null,
                'question_ids' => $selectedQuestions->pluck('id')->toArray(),
            ]);
        }

        // Always redirect to takeExam with the attempt id
        return redirect()->route('exam.take', [$examName, 'attempt' => $attempt->id]);
    }
}

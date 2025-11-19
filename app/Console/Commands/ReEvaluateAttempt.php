<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Attempt;
use App\Models\Question;
use App\Models\Response;
use Illuminate\Support\Facades\DB;

class ReEvaluateAttempt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exam:re-evaluate {attempt_id : The ID of the attempt to re-evaluate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Re-evaluates an exam attempt based on the current correct answers.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $attemptId = $this->argument('attempt_id');
        $attempt = Attempt::with(['exam', 'responses'])->find($attemptId);

        if (!$attempt) {
            $this->error("Attempt with ID {$attemptId} not found.");
            return 1;
        }

        $this->info("Re-evaluating Attempt ID: {$attempt->id} (User: {$attempt->user_cid})");
        $this->info("Current Score: {$attempt->score}");
        $this->info("Current Status: {$attempt->status}");

        $exam = $attempt->exam;
        $questionIds = $attempt->question_ids ?? [];
        
        if (empty($questionIds)) {
            $this->error("No questions found for this attempt.");
            return 1;
        }

        $questions = Question::whereIn('id', $questionIds)->with('answers')->get()->keyBy('id');
        $responses = $attempt->responses->keyBy('question_id');

        $totalScore = 0;
        $maxScore = $questions->sum('points');
        $hasPendingEvaluation = false;

        $this->line("Processing " . count($questionIds) . " questions...");

        foreach ($questionIds as $qId) {
            if (!isset($questions[$qId])) {
                $this->warn("Question ID {$qId} not found in database. Skipping.");
                continue;
            }

            $question = $questions[$qId];
            $response = $responses[$qId] ?? null;
            $userResponseText = $response ? $response->response_text : null;
            
            $score = 0;
            $isCorrect = false;
            $status = 'incorrect';

            if ($question->question_type === 'mcq') {
                $correctAnswers = $question->answers->where('is_correct', true)->pluck('id')->map(fn($id) => (string) $id)->sort()->values()->toArray();
                $userAnswers = $userResponseText ? explode(',', $userResponseText) : [];
                sort($userAnswers);

                if ($correctAnswers === $userAnswers) {
                    $score = $question->points;
                    $isCorrect = true;
                    $status = 'correct';
                }
            } elseif ($question->question_type === 'single') {
                $correctAnswers = $question->answers->where('is_correct', true)->pluck('id')->map(fn($id) => (string) $id)->toArray();
                
                if ($userResponseText && in_array($userResponseText, $correctAnswers)) {
                    $score = $question->points;
                    $isCorrect = true;
                    $status = 'correct';
                }
            } elseif ($question->question_type === 'text') {
                $status = 'pending';
                $hasPendingEvaluation = true;
            }

            $totalScore += $score;

            // Update response status if it exists
            if ($response) {
                $response->update(['status' => $status]);
            }
        }

        // Determine final status
        if ($hasPendingEvaluation) {
            $finalStatus = 'waiting_for_manual_evaluation';
        } else {
            // Assuming 80% pass rate as per ExamController
            $passRate = 0.8;
            $finalStatus = ($totalScore / $maxScore) >= $passRate ? 'passed' : 'failed';
        }

        $this->info("------------------------------------------------");
        $this->info("New Score: {$totalScore} / {$maxScore}");
        $this->info("New Status: {$finalStatus}");

        if ($this->confirm('Do you want to save these changes?', true)) {
            $attempt->update([
                'score' => $totalScore,
                'status' => $finalStatus,
            ]);
            $this->info("Attempt updated successfully.");
        } else {
            $this->info("Changes discarded.");
        }

        return 0;
    }
}

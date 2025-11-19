<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Question;
use App\Models\Answer;
use Illuminate\Support\Facades\DB;

class FixMissingCorrectAnswers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:missing-correct-answers {--dry-run : Run without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fixes questions that are missing correct answers by copying from duplicates that have them.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $this->info("Starting fix process..." . ($dryRun ? " (DRY RUN)" : ""));

        // 1. Group questions by text
        $groupedQuestions = Question::select('question_text')
            ->groupBy('question_text')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        $this->info("Found " . $groupedQuestions->count() . " groups of duplicated questions.");

        $fixedCount = 0;

        foreach ($groupedQuestions as $group) {
            $questions = Question::where('question_text', $group->question_text)->with('answers')->get();

            // 2. Find a "source" question (one that has at least one correct answer)
            $source = $questions->first(function ($q) {
                return $q->answers->where('is_correct', true)->count() > 0;
            });

            if (!$source) {
                // $this->warn("No source found for question: " . substr(strip_tags($group->question_text), 0, 50) . "...");
                continue;
            }

            // 3. Find "target" questions (ones that have NO correct answers)
            $targets = $questions->filter(function ($q) {
                return $q->answers->where('is_correct', true)->count() === 0;
            });

            if ($targets->isEmpty()) {
                continue;
            }

            $this->info("Processing group: " . substr(strip_tags($group->question_text), 0, 50) . "...");
            $this->line("  Source ID: {$source->id} (Exam: {$source->exam_id})");

            foreach ($targets as $target) {
                $this->line("  Target ID: {$target->id} (Exam: {$target->exam_id}) - Fixing...");

                if ($dryRun) {
                    continue;
                }

                DB::transaction(function () use ($source, $target) {
                    $sourceAnswers = $source->answers;
                    $targetAnswers = $target->answers;

                    foreach ($targetAnswers as $tAnswer) {
                        // Find matching answer in source
                        $matchingSourceAnswer = $sourceAnswers->first(function ($sAnswer) use ($tAnswer) {
                            return trim($sAnswer->answer_text) === trim($tAnswer->answer_text);
                        });

                        if ($matchingSourceAnswer && $matchingSourceAnswer->is_correct) {
                            $tAnswer->update(['is_correct' => true]);
                            $this->info("    - Marked answer '{$tAnswer->answer_text}' as correct.");
                        }
                    }
                });
                $fixedCount++;
            }
        }

        $this->info("Done. Fixed {$fixedCount} questions.");

        return 0;
    }
}

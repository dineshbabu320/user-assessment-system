<?php

use Illuminate\Foundation\Console\ClosureCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Artisan;
use App\Models\Question;
use App\Models\Answer;
use Illuminate\Support\Facades\DB;

Artisan::command('inspire', function () {
    /** @var ClosureCommand $this */
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('tokens:expire')->everyMinute();
Schedule::command('clear:audit-logs')->daily();

// Maintenance: Backfill answers from options when only correct answers were saved
Artisan::command('maintenance:backfill-answers {--dry-run} {--question-id=} {--chunk=200} {--prune}', function () {
    /** @var ClosureCommand $this */
    $dryRun = (bool) $this->option('dry-run');
    $prune = (bool) $this->option('prune');
    $questionId = $this->option('question-id');
    $chunk = (int) $this->option('chunk');

    $fixed = 0; $skipped = 0; $created = 0; $updated = 0; $deleted = 0;

    $base = Question::with('answers')
        ->whereIn('question_type', ['mcq', 'single']);
    if ($questionId) { $base->where('id', $questionId); }

    $base->chunkById(max($chunk, 1), function ($batch) use (&$fixed, &$skipped, &$created, &$updated, &$deleted, $dryRun, $prune) {
        foreach ($batch as $q) {
            $optsRaw = $q->options;
            $opts = $optsRaw ? (is_array($optsRaw) ? $optsRaw : json_decode($optsRaw, true)) : [];
            if (!$opts || !is_array($opts) || empty($opts)) { $skipped++; continue; }

            // Normalize options: trim strings
            $opts = array_values(array_map(fn($s) => is_string($s) ? trim($s) : $s, $opts));

            $existing = $q->answers()->get();
            $existingByText = $existing->keyBy('answer_text');

            $needsFix = false;
            if ($existingByText->count() < count($opts)) { $needsFix = true; }
            else {
                foreach ($opts as $opt) { if (!$existingByText->has($opt)) { $needsFix = true; break; } }
            }
            if (!$needsFix && !$prune) { $skipped++; continue; }

            $wouldCreate = []; $wouldUpdate = []; $wouldDelete = [];

            $apply = function () use ($q, $opts, $existingByText, $prune, &$created, &$updated, &$deleted) {
                DB::transaction(function () use ($q, $opts, $existingByText, $prune, &$created, &$updated, &$deleted) {
                    $updatedTexts = [];
                    foreach ($opts as $opt) {
                        if ($existingByText->has($opt)) {
                            // Ensure row exists; preserve current is_correct
                            $answer = $existingByText->get($opt);
                            // no-op update ensures touched row if needed
                            $answer->update(['is_correct' => (bool) $answer->is_correct]);
                            $updated++; $updatedTexts[] = $opt;
                        } else {
                            Answer::create([
                                'question_id' => $q->id,
                                'answer_text' => $opt,
                                'is_correct' => false,
                            ]);
                            $created++; $updatedTexts[] = $opt;
                        }
                    }
                    if ($prune) {
                        $delCount = Answer::where('question_id', $q->id)
                            ->whereNotIn('answer_text', $updatedTexts)
                            ->delete();
                        $deleted += $delCount;
                    }
                });
            };

            if ($dryRun) {
                foreach ($opts as $opt) {
                    if ($existingByText->has($opt)) { $wouldUpdate[] = $opt; } else { $wouldCreate[] = $opt; }
                }
                if ($prune) {
                    foreach ($existingByText->keys() as $txt) { if (!in_array($txt, $opts, true)) { $wouldDelete[] = $txt; } }
                }
                $this->line("[DRY-RUN] Q#{$q->id}: create=".json_encode($wouldCreate)." update=".json_encode($wouldUpdate).($prune ? " delete=".json_encode($wouldDelete) : ''));
            } else {
                $apply();
                $fixed++;
            }
        }
    });

    if ($dryRun) {
        $this->info("Backfill DRY-RUN complete. Skipped: {$skipped}");
    } else {
        $this->info("Backfill complete. Fixed: {$fixed}, Skipped: {$skipped}, Created: {$created}, Updated: {$updated}, Deleted: {$deleted}");
    }
})->purpose('Backfill answer options from stored question options for MCQ/Single questions');
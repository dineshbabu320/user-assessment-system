<?php

namespace App\Console\Commands;

use App\Models\Exam;
use App\Models\User;
use Illuminate\Console\Command;

class test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test {examId} {userId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $exam = Exam::findOrFail($this->argument('examId'));
        $user = User::findOrFail($this->argument('userId'));
        $ratios = $exam->category_ratios ?? [];
        $totalQuestions = $exam->question_limit;
        $selectedQuestions = collect();

        foreach ($ratios as $category => $count) {
            if (empty($count) || intval($count) < 1) {
                continue; // Skip if 0, null, or not set
            }
            if ($category === 'local' && $user->subdivision_id) {
                $qs = $exam->questions()
                    ->where('category', 'local')
                    ->where('subdivision_id', $user->subdivision_id)
                    ->inRandomOrder()
                    ->take($count)
                    ->get();
            } else {
                $qs = $exam->questions()
                    ->where('category', $category)
                    ->whereNull('subdivision_id')
                    ->inRandomOrder()
                    ->take($count)
                    ->get();
            }
            $selectedQuestions = $selectedQuestions->merge($qs);
        }

        $shortfall = $totalQuestions - $selectedQuestions->count();
        if ($shortfall > 0) {
            $alreadySelectedIds = $selectedQuestions->pluck('id')->toArray();
            $randomQs = $exam->questions()
                ->whereNotIn('id', $alreadySelectedIds)
                ->whereNull('subdivision_id')
                ->inRandomOrder()
                ->take($shortfall)
                ->get();
            $selectedQuestions = $selectedQuestions->merge($randomQs);
        }

        $selectedQuestions = $selectedQuestions->shuffle()->take($totalQuestions);

        // Output for inspection
        foreach ($selectedQuestions as $q) {
            $this->line("QID: {$q->id} | Category: {$q->category} | Subdivision: {$q->subdivision_id}");
        }
    }
}

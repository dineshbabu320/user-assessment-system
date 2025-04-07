<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RatingUpgradeRequest;

class CleanupRatingUpgradeRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleanup:rating-upgrade-requests';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up completed rating upgrade requests';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        RatingUpgradeRequest::where('completed', true)->delete();
        $this->info('Completed rating upgrade requests cleaned up.');
    }
}

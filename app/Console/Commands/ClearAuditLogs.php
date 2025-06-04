<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ActivityLog;
use Carbon\Carbon;

class ClearAuditLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:audit-logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clears audit logs older than 90 days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = Carbon::now()->subDays(90);
        $deleted = ActivityLog::where('created_at','<',$date)->delete();

        $this->info("Deleted {$deleted} records");
    }
}

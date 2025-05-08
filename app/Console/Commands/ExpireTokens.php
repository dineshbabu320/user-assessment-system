<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Token;
use Carbon\Carbon;

class ExpireTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tokens:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set tokens to expired if their expiry date has passed';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $now = Carbon::now();

        // Find all active tokens where the expiry date has passed
        $expiredTokens = Token::where('status', 'active')
            ->where('expires_at', '<', $now)
            ->update(['status' => 'expired']);

        $this->info("Expired {$expiredTokens} tokens.");

        return 0;
    }
}

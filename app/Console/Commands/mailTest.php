<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class mailTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:mail-test';

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
        $user = User::find(10000006);
        $subdivisionDirectors = User::where('subdivision_id', $user->subdivision_id)
            ->whereHas('roles', function ($q) {
                $q->where('id', 2);
            })
            ->pluck('notification_email', 'email')
            ->map(function($notification_email, $email) {
                return $notification_email ?: $email;
            })
            ->values()
            ->toArray();

        $adminEmails = User::whereHas('subdivisionNotifications', function ($q) use ($user) {
            $q->where('subdivision_id', $user->subdivision_id);
            })
            ->pluck('notification_email', 'email')
            ->map(function($notification_email, $email) {
                return $notification_email ?: $email;
            })
            ->values()
            ->toArray();
        print_r($adminEmails); print_r($subdivisionDirectors);
    }
}

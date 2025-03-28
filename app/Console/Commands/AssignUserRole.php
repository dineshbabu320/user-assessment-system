<?php
namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class AssignUserRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:assign-role {user_id} {role_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign a role to a user by updating the role_id field in the users table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get the input arguments
        $userId = $this->argument('user_id');
        $roleId = $this->argument('role_id');

        // Find the user by ID
        $user = User::find($userId);

        if (!$user) {
            $this->error("User with ID {$userId} not found.");
            return 1; // Return error code
        }

        // Update the user's role_id
        $user->role_id = $roleId;
        $user->save();

        $this->info("Role ID {$roleId} has been assigned to user with ID {$userId}.");
        return 0; // Return success code
    }
}
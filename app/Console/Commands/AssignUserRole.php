<?php
namespace App\Console\Commands;

use App\Models\User;
use App\Models\Role;
use Illuminate\Console\Command;

class AssignUserRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:assign-role {user_cid} {role_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign a role to a user using the pivot table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userCid = $this->argument('user_cid');
        $roleId = $this->argument('role_id');

        $user = User::where('cid', $userCid)->first();
        $role = Role::find($roleId);

        if (!$user) {
            $this->error("User with CID {$userCid} not found.");
            return 1;
        }
        if (!$role) {
            $this->error("Role with ID {$roleId} not found.");
            return 1;
        }

        // Attach role, no duplicate
        $user->roles()->syncWithoutDetaching([$roleId]);

        $this->info("Role '{$role->name}' (ID {$roleId}) assigned to user {$user->name} (CID {$userCid}).");
        return 0;
    }
}
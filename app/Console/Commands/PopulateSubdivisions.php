<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subdivision;

class PopulateSubdivisions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subdivisions:populate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate subdivisions table with existing subdivision IDs from users table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $subdivisions = [
            'SING',
            'VCL', 
            'IDN',
            'MYS',
            'THA',
            'HK',
            'PHL',
        ];

        $this->info('Populating subdivisions table...');

        foreach ($subdivisions as $code) {
            $subdivision = Subdivision::firstOrCreate(['name' => $code]);

            if ($subdivision->wasRecentlyCreated) {
                $this->info("Created subdivision: {$code}");
            } else {
                $this->warn("Subdivision already exists: {$code}");
            }
        }

        $this->success('Subdivisions population completed!');
    }
}

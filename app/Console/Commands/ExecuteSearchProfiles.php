<?php

namespace App\Console\Commands;

use App\Jobs\DispatchSearchProfileJobs;
use Illuminate\Console\Command;

class ExecuteSearchProfiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:execute-search-profiles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute all Search Profiles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Search Profile Execution...');

        DispatchSearchProfileJobs::dispatch();

        return Command::SUCCESS;
    }
}

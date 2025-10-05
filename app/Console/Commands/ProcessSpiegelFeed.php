<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\SpiegelRSSProcessor;
use Illuminate\Console\Command;

final class ProcessSpiegelFeed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:process-spiegel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process Spiegel RSS feed and fetch articles';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting Spiegel RSS feed processing...');

        // Dispatch the job
        SpiegelRSSProcessor::dispatch();

        $this->info('SpiegelRSSProcessor job dispatched successfully.');

        return Command::SUCCESS;
    }
}

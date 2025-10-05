<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\HeiseRSSProcessor;
use Illuminate\Console\Command;

final class ProcessHeiseFeed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:process-heise';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process Heise RSS feed and fetch articles';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting Heise RSS feed processing...');

        // Dispatch the job
        HeiseRSSProcessor::dispatch();

        $this->info('HeiseRSSProcessor job dispatched successfully.');

        return Command::SUCCESS;
    }
}

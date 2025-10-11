<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\VolksverpetzerRSSProcessor;
use Illuminate\Console\Command;

final class ProcessVolksverpetzerFeed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:process-volksverpetzer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process Volksverpetzer RSS feed and fetch articles';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting Volksverpetzer RSS feed processing...');

        // Dispatch the job
        VolksverpetzerRSSProcessor::dispatch();

        $this->info('VolksverpetzerRSSProcessor job dispatched successfully.');

        return Command::SUCCESS;
    }
}

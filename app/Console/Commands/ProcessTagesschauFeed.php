<?php

namespace App\Console\Commands;

use App\Jobs\TagesschauRSSProcessor;
use Illuminate\Console\Command;

class ProcessTagesschauFeed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:process-tagesschau';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process Tagesschau RSS feed and fetch articles';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting Tagesschau RSS feed processing...');

        // Dispatch the job
        TagesschauRSSProcessor::dispatch();

        $this->info('TagesschauRSSProcessor job dispatched successfully.');

        return Command::SUCCESS;
    }
}

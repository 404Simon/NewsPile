<?php

namespace App\Jobs;

use App\Models\SearchProfile;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class DispatchSearchProfileJobs implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        SearchProfile::query()->chunk(100, function ($searchProfiles) {
            foreach ($searchProfiles as $searchProfile) {
                ProcessSearchProfile::dispatch($searchProfile);
            }
        });
    }
}

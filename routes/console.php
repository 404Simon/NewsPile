<?php

use App\Jobs\DispatchSearchProfileJobs;
use App\Jobs\TagesschauRSSProcessor;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new TagesschauRSSProcessor)->everyFifteenMinutes();
Schedule::job(new DispatchSearchProfileJobs)->everyFifteenMinutes();

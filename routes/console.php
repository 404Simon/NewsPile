<?php

declare(strict_types=1);

use App\Jobs\DispatchSearchProfileJobs;
use App\Jobs\HeiseRSSProcessor;
use App\Jobs\SpiegelRSSProcessor;
use App\Jobs\TagesschauRSSProcessor;
use App\Jobs\VolksverpetzerRSSProcessor;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new TagesschauRSSProcessor)->everyFifteenMinutes();
Schedule::job(new SpiegelRSSProcessor)->everyFifteenMinutes();
Schedule::job(new HeiseRSSProcessor)->everyFifteenMinutes();
Schedule::job(new VolksverpetzerRSSProcessor)->everyFifteenMinutes();

Schedule::job(new DispatchSearchProfileJobs)->everyFifteenMinutes();

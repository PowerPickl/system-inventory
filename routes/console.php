<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// EOQ Calculation Scheduler
Schedule::job(new \App\Jobs\UpdateEOQCalculations())
        ->everySixHours()
        ->withoutOverlapping()
        ->onOneServer();

// Force update daily at 2 AM  
Schedule::job(new \App\Jobs\UpdateEOQCalculations(null, true))
        ->dailyAt('02:00')
        ->withoutOverlapping()
        ->onOneServer();
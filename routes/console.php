<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// command test
Artisan::command('test', function () {
    $this->comment('OK');
});

// 🔥 SCHEDULER
Schedule::command('reminder:due')->dailyAt('08:00');

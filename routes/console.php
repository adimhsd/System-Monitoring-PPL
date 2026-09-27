<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Cron Scheduler untuk menjalankan Queue Worker secara otomatis
Schedule::command('queue:work --stop-when-empty')->everyMinute();

<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Add your appointment reminder schedule here
// This will run every day at 8:00 PM (20:00)

// 1. STAFF DAILY SCHEDULE (5:00 AM) - Sent to nurses and health workers
Schedule::command('staff:send-daily-schedule')
    ->dailyAt('05:00')
    ->timezone('Asia/Manila')
    ->emailOutputOnFailure('admin@yourhealthcenter.com');

Schedule::command('staff:send-overdue-notifications')
    ->dailyAt('09:00')
    ->timezone('Asia/Manila')
    ->emailOutputOnFailure('admin@yourhealthcenter.com');
// for patients
Schedule::command('appointments:send-reminders')
    ->dailyAt('20:00')
    ->timezone('Asia/Manila') // Adjust to your timezone
    ->emailOutputOnFailure('admin@yourhealthcenter.com'); // Optional: get email if it fails

  

// Alternative schedule options you can use:
// ->dailyAt('19:00')                    // At 7:00 PM
// ->dailyAt('21:00')                    // At 9:00 PM
// ->twiceDaily(8, 20)                   // At 8:00 AM and 8:00 PM
// ->weekdays()->dailyAt('20:00')        // Only on weekdays at 8:00 PM
// ->days([1, 2, 3, 4, 5])->dailyAt('20:00') // Monday to Friday at 8:00 PM

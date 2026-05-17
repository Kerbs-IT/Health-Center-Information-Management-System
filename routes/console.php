<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ── Staff daily schedule (5:00 AM) ────────────────────────────────────────────
Schedule::command('staff:send-daily-schedule')
    ->dailyAt('05:00')
    ->timezone('Asia/Manila')
    ->emailOutputOnFailure('admin@yourhealthcenter.com');

// ── Overdue appointment notifications (9:00 AM) ───────────────────────────────
Schedule::command('staff:send-overdue-notifications')
    ->dailyAt('09:00')
    ->timezone('Asia/Manila')
    ->emailOutputOnFailure('admin@yourhealthcenter.com');

// ── Patient appointment reminders (8:00 PM) ───────────────────────────────────
Schedule::command('appointments:send-reminders')
    ->dailyAt('20:00')
    ->timezone('Asia/Manila')
    ->emailOutputOnFailure('admin@yourhealthcenter.com');

// ── Recalculate batch expiry statuses (midnight) ──────────────────────────────
// Runs first so that expiry_status values are fresh before the 8 AM check.
Schedule::command('batches:recalculate-expiry')
    ->dailyAt('00:01')
    ->timezone('Asia/Manila');

// ── Inventory check + daily digest email (8:00 AM) ───────────────────────────
//
//  This single command does TWO things:
//  1. Sends bell notifications + immediate individual emails for every
//     Expired / Expiring Soon batch found (skips duplicates for today).
//  2. Sends one daily digest email to every nurse/staff showing:
//       • Out of Stock medicines
//       • Low Stock medicines
//       • Expiring Soon batches
//       • Expired batches
//
Schedule::command('inventory:check-expiry')
    ->dailyAt('08:00')
    ->timezone('Asia/Manila')
    ->emailOutputOnFailure('admin@yourhealthcenter.com');
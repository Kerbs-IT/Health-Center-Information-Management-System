<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Medicine;
use App\Models\MedicineBatch;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\InventoryAlertMail;
use App\Mail\InventoryDailyDigestMail;
use Carbon\Carbon;

class CheckInventoryExpiry extends Command
{
    protected $signature   = 'inventory:check-expiry';
    protected $description = 'Daily 8 AM run: sends bell + individual email for expiry/stock alerts, then sends the daily digest email to all staff/nurses.';

    public function handle(): void
    {
        // ── STEP 1: Expiry bell notifications + individual emails ───
        $this->processExpiryAlerts();

        // ── STEP 2: Stock level bell notifications + individual emails
        $this->processStockAlerts();

        // ── STEP 3: Daily digest email (8 AM summary) ───────────────
        $this->sendDailyDigest();

        $this->info('inventory:check-expiry completed.');
    }

    // ─────────────────────────────────────────────────────────────
    // STEP 1 — Per-batch expiry bell + individual email per user
    //
    // BUG FIXED: Recipients are now re-fetched fresh inside this
    // method so updated emails are always used, not a stale
    // in-memory collection from handle().
    //
    // BUG FIXED: Bell-insert dedup and email-send dedup are now
    // separated. The bell check prevents duplicate in-app
    // notifications. The email check is done independently per
    // user so each person gets their own correctly-named email.
    // ─────────────────────────────────────────────────────────────

    private function processExpiryAlerts(): void
    {
        // Always re-fetch so updated emails/names are used
        $recipients = User::whereIn('role', ['nurse', 'staff'])->get();

        if ($recipients->isEmpty()) {
            $this->warn('No nurse/staff users found.');
            return;
        }

        $batches = MedicineBatch::with('medicine.category')
            ->whereNull('deleted_at')
            ->whereIn('expiry_status', ['Expiring Soon', 'Expired'])
            ->get();

        if ($batches->isEmpty()) {
            $this->info('No expiring/expired batches found.');
            return;
        }

        foreach ($batches as $batch) {
            $medicine = $batch->medicine;
            if (! $medicine) continue;

            $alertType = $batch->expiry_status === 'Expired' ? 'expired' : 'expiring_soon';
            $title     = $this->buildTitle($alertType);
            $message   = $this->buildBatchMessage($alertType, $medicine, $batch);

            foreach ($recipients as $user) {

                // ── Bell dedup: skip insert if already notified today ──
                $bellAlreadySent = DB::table('notifications')
                    ->where('user_id',          $user->id)
                    ->where('type',             $alertType)
                    ->where('appointment_type', 'inventory')
                    ->where('message',          $message)
                    ->whereDate('created_at',   today())
                    ->exists();

                if (! $bellAlreadySent) {
                    DB::table('notifications')->insert([
                        'user_id'          => $user->id,
                        'type'             => $alertType,
                        'title'            => $title,
                        'message'          => $message,
                        'appointment_type' => 'inventory',
                        'link_url'         => '/medicines?filter=' . $alertType,
                        'is_read'          => 0,
                        'created_at'       => now(),
                        'updated_at'       => now(),
                    ]);
                }

                // ── Email dedup: separate check so updated email is ────
                // always evaluated independently from the bell check ────
                $emailAlreadySent = DB::table('inventory_email_logs')
                    ->where('user_id',     $user->id)
                    ->where('alert_type',  $alertType)
                    ->where('reference',   $message)
                    ->whereDate('sent_at', today())
                    ->exists();

                if ($emailAlreadySent) continue;

                try {
                    // BUG FIXED: Pass $user (not a shared primary) so
                    // each recipient sees their OWN name in the greeting.
                    Mail::to($user->email)->send(new InventoryAlertMail(
                        $alertType,
                        $medicine,
                        $batch->batch_number,
                        $batch->expiry_date->format('M d, Y'),
                        $user   // ← correct: each user gets their own greeting
                    ));

                    // Log the email so we don't re-send today
                    DB::table('inventory_email_logs')->insert([
                        'user_id'    => $user->id,
                        'alert_type' => $alertType,
                        'reference'  => $message,
                        'sent_at'    => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $this->info("Alert email sent to {$user->email}: {$medicine->medicine_name} — {$alertType}");
                } catch (\Exception $e) {
                    Log::error("InventoryAlertMail failed for {$user->email}: " . $e->getMessage());
                    $this->error("Mail failed for {$user->email}: " . $e->getMessage());
                }
            }

            $this->info("Expiry alert processed: {$medicine->medicine_name} — {$alertType}");
        }
    }

    // ─────────────────────────────────────────────────────────────
    // STEP 2 — Stock level alerts (out-of-stock / low-stock)
    //
    // BUG FIXED: Stock updates were never triggering notifications
    // because this check did not exist in the original command.
    // Now each medicine whose stock dropped to 0 or entered the
    // low-stock zone within the last 24 hours gets a bell
    // notification AND an individually-addressed email per user.
    // ─────────────────────────────────────────────────────────────

    private function processStockAlerts(): void
    {
        // Re-fetch fresh recipients
        $recipients = User::whereIn('role', ['nurse', 'staff'])->get();
        if ($recipients->isEmpty()) return;

        $windowStart = Carbon::now()->subDay();

        // Out of stock: stock hit 0 within last 24 hours
        $outOfStock = Medicine::whereNull('deleted_at')
            ->where('stock', '<=', 0)
            ->where('updated_at', '>=', $windowStart)
            ->with('category')
            ->get();

        // Low stock: stock is between 1–10, updated in last 24 hours
        $lowStock = Medicine::whereNull('deleted_at')
            ->where('stock', '>', 0)
            ->where('stock', '<=', 10)
            ->where('updated_at', '>=', $windowStart)
            ->with('category')
            ->get();

        $allStockAlerts = [
            'out_of_stock' => $outOfStock,
            'low_stock'    => $lowStock,
        ];

        foreach ($allStockAlerts as $alertType => $medicines) {
            foreach ($medicines as $medicine) {
                $title   = $this->buildTitle($alertType);
                $message = $this->buildStockMessage($alertType, $medicine);

                foreach ($recipients as $user) {

                    // ── Bell dedup ─────────────────────────────────────
                    $bellAlreadySent = DB::table('notifications')
                        ->where('user_id',          $user->id)
                        ->where('type',             $alertType)
                        ->where('appointment_type', 'inventory')
                        ->where('message',          $message)
                        ->whereDate('created_at',   today())
                        ->exists();

                    if (! $bellAlreadySent) {
                        DB::table('notifications')->insert([
                            'user_id'          => $user->id,
                            'type'             => $alertType,
                            'title'            => $title,
                            'message'          => $message,
                            'appointment_type' => 'inventory',
                            'link_url'         => '/medicines?filter=' . $alertType,
                            'is_read'          => 0,
                            'created_at'       => now(),
                            'updated_at'       => now(),
                        ]);
                    }

                    // ── Email dedup (independent from bell) ────────────
                    $emailAlreadySent = DB::table('inventory_email_logs')
                        ->where('user_id',     $user->id)
                        ->where('alert_type',  $alertType)
                        ->where('reference',   $message)
                        ->whereDate('sent_at', today())
                        ->exists();

                    if ($emailAlreadySent) continue;

                    try {
                        // Reuses InventoryAlertMail — already handles
                        // out_of_stock and low_stock in its match()
                        Mail::to($user->email)->send(new InventoryAlertMail(
                            $alertType,
                            $medicine,
                            null,   // no batch number for stock alerts
                            null,   // no expiry date for stock alerts
                            $user   // ← individual greeting per recipient
                        ));

                        DB::table('inventory_email_logs')->insert([
                            'user_id'    => $user->id,
                            'alert_type' => $alertType,
                            'reference'  => $message,
                            'sent_at'    => now(),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        $this->info("Stock alert email sent to {$user->email}: {$medicine->medicine_name} — {$alertType}");
                    } catch (\Exception $e) {
                        Log::error("InventoryAlertMail (stock) failed for {$user->email}: " . $e->getMessage());
                        $this->error("Mail failed for {$user->email}: " . $e->getMessage());
                    }
                }

                $this->info("Stock alert processed: {$medicine->medicine_name} — {$alertType}");
            }
        }
    }

    // ─────────────────────────────────────────────────────────────
    // STEP 3 — Daily digest (yesterday 8 AM → today 8 AM window)
    //
    // BUG FIXED: Recipients re-fetched fresh so updated emails
    // are used. Each recipient now gets their own named email
    // instead of everyone getting the primary user's greeting.
    // ─────────────────────────────────────────────────────────────

    private function sendDailyDigest(): void
    {
        // Re-fetch fresh so updated emails/names are always used
        $recipients = User::whereIn('role', ['nurse', 'staff'])->get();

        if ($recipients->isEmpty()) {
            $this->warn('No nurse/staff users found for digest.');
            return;
        }

        $windowStart = Carbon::today('Asia/Manila')->subDay()->setTime(8, 0, 0);
        $windowEnd   = Carbon::today('Asia/Manila')->setTime(8, 0, 0);

        $outOfStock = Medicine::whereNull('deleted_at')
            ->where('stock', '<=', 0)
            ->with('category')
            ->whereBetween('updated_at', [$windowStart, $windowEnd])
            ->get();

        $lowStock = Medicine::whereNull('deleted_at')
            ->where('stock', '>', 0)
            ->where('stock', '<=', 10)
            ->with('category')
            ->whereBetween('updated_at', [$windowStart, $windowEnd])
            ->get();

        $expiringSoon = MedicineBatch::with('medicine.category')
            ->whereNull('deleted_at')
            ->where('expiry_status', 'Expiring Soon')
            ->whereBetween('updated_at', [$windowStart, $windowEnd])
            ->get();

        $expired = MedicineBatch::with('medicine.category')
            ->whereNull('deleted_at')
            ->where('expiry_status', 'Expired')
            ->whereBetween('updated_at', [$windowStart, $windowEnd])
            ->get();

        // BUG FIXED: Send individually per user so each person's
        // name appears correctly in their own digest email.
        foreach ($recipients as $user) {

            // Digest dedup: one digest per user per day
            $digestAlreadySent = DB::table('inventory_email_logs')
                ->where('user_id',     $user->id)
                ->where('alert_type',  'daily_digest')
                ->whereDate('sent_at', today())
                ->exists();

            if ($digestAlreadySent) continue;

            try {
                Mail::to($user->email)->send(new InventoryDailyDigestMail(
                    $user,          // ← each user gets their own greeting
                    $outOfStock,
                    $lowStock,
                    $expiringSoon,
                    $expired,
                    $windowStart,
                    $windowEnd
                ));

                DB::table('inventory_email_logs')->insert([
                    'user_id'    => $user->id,
                    'alert_type' => 'daily_digest',
                    'reference'  => 'daily_digest_' . today()->toDateString(),
                    'sent_at'    => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $this->info("Daily digest sent to {$user->email}.");
            } catch (\Exception $e) {
                Log::error("InventoryDailyDigestMail failed for {$user->email}: " . $e->getMessage());
                $this->error("Digest mail failed for {$user->email}: " . $e->getMessage());
            }
        }

        $this->info("Daily digest step completed for " . $recipients->count() . " recipient(s).");
    }

    // ─────────────────────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────────────────────

    private function buildTitle(string $alertType): string
    {
        return match ($alertType) {
            'expired'       => '⛔ Expired Medicine Alert',
            'expiring_soon' => '🟠 Expiring Soon Alert',
            'out_of_stock'  => '🔴 Out of Stock Alert',
            'low_stock'     => '🟡 Low Stock Alert',
            default         => '⚠️ Inventory Alert',
        };
    }

    private function buildBatchMessage(string $alertType, $medicine, $batch): string
    {
        return match ($alertType) {
            'expired'       => "{$medicine->medicine_name} ({$medicine->dosage}) batch {$batch->batch_number} has EXPIRED as of {$batch->expiry_date->format('M d, Y')}.",
            'expiring_soon' => "{$medicine->medicine_name} ({$medicine->dosage}) batch {$batch->batch_number} is expiring on {$batch->expiry_date->format('M d, Y')}.",
            default         => "Inventory alert for {$medicine->medicine_name}.",
        };
    }

    private function buildStockMessage(string $alertType, $medicine): string
    {
        return match ($alertType) {
            'out_of_stock' => "{$medicine->medicine_name} ({$medicine->dosage}) is OUT OF STOCK. Current stock: {$medicine->stock}.",
            'low_stock'    => "{$medicine->medicine_name} ({$medicine->dosage}) is running LOW. Current stock: {$medicine->stock}.",
            default        => "Stock alert for {$medicine->medicine_name}.",
        };
    }
}

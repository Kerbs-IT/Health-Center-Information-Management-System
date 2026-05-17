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
    protected $description = 'Daily 8 AM run: sends bell + batch email for expiry alerts, then sends the daily digest email to all staff/nurses.';

    public function handle(): void
    {
        $recipients = User::whereIn('role', ['nurse', 'staff'])->get();

        if ($recipients->isEmpty()) {
            $this->warn('No nurse/staff users found.');
            return;
        }

        // ── STEP 1: Expiry bell notifications + batch email ────────
        $this->processExpiryAlerts($recipients);

        // ── STEP 2: Daily digest email (8 AM summary) ──────────────
        $this->sendDailyDigest($recipients);

        $this->info('inventory:check-expiry completed.');
    }

    // ─────────────────────────────────────────────────────────────
    // STEP 1 — Per-batch expiry bell + ONE batch email to all
    // ─────────────────────────────────────────────────────────────

    private function processExpiryAlerts($recipients): void
    {
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

            // ── Collect recipients who haven't been notified today ──
            $emailRecipients = [];

            foreach ($recipients as $user) {
                $alreadySent = DB::table('notifications')
                    ->where('user_id',          $user->id)
                    ->where('type',             $alertType)
                    ->where('appointment_type', 'inventory')
                    ->where('message',          $message)
                    ->whereDate('created_at',   today())
                    ->exists();

                if ($alreadySent) continue;

                // ── Bell notification ──────────────────────────────
                DB::table('notifications')->insert([
                    'user_id'          => $user->id,
                    'type'             => $alertType,
                    'title'            => $title,
                    'message'          => $message,
                    'appointment_type' => 'inventory',
                    'link_url' => '/medicines?filter=' . $alertType,
                    'is_read'          => 0,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);

                $emailRecipients[] = $user;
            }

            // ── ONE batch email to all qualifying recipients ────────
            if (! empty($emailRecipients)) {
                try {
                    $primary = array_shift($emailRecipients); // first recipient as To:

                    $mailer = Mail::to($primary->email);

                    // Add the rest as BCC so addresses stay private
                    foreach ($emailRecipients as $bccUser) {
                        $mailer->bcc($bccUser->email);
                    }

                    $mailer->send(new InventoryAlertMail(
                        $alertType,
                        $medicine,
                        $batch->batch_number,
                        $batch->expiry_date->format('M d, Y'),
                        $primary          // greeting uses primary; others see their own inbox name
                    ));

                    $this->info("Alert email sent: {$medicine->medicine_name} — {$alertType}");
                } catch (\Exception $e) {
                    Log::error("InventoryAlertMail batch send failed: " . $e->getMessage());
                    $this->error("Mail failed: " . $e->getMessage());
                }
            }

            $this->info("Expiry alert processed: {$medicine->medicine_name} — {$alertType}");
        }
    }

    // ─────────────────────────────────────────────────────────────
    // STEP 2 — Daily digest (yesterday 8 AM → today 8 AM window)
    // ─────────────────────────────────────────────────────────────

    private function sendDailyDigest($recipients): void
    {
        // Time window: yesterday 08:00 → today 08:00 (Asia/Manila)
        $windowStart = Carbon::today('Asia/Manila')->subDay()->setTime(8, 0, 0);
        $windowEnd   = Carbon::today('Asia/Manila')->setTime(8, 0, 0);

        // ── Out of stock — medicines that dropped to 0 in window ───
        $outOfStock = Medicine::whereNull('deleted_at')
            ->where('stock', '<=', 0)
            ->with('category')
            ->whereBetween('updated_at', [$windowStart, $windowEnd])
            ->get();

        // ── Low stock — medicines that entered low zone in window ──
        $lowStock = Medicine::whereNull('deleted_at')
            ->where('stock', '>', 0)
            ->where('stock', '<=', 10)
            ->with('category')
            ->whereBetween('updated_at', [$windowStart, $windowEnd])
            ->get();

        // ── Expiring soon — batches flagged in window ──────────────
        $expiringSoon = MedicineBatch::with('medicine.category')
            ->whereNull('deleted_at')
            ->where('expiry_status', 'Expiring Soon')
            ->whereBetween('updated_at', [$windowStart, $windowEnd])
            ->get();

        // ── Expired — batches that expired in window ───────────────
        $expired = MedicineBatch::with('medicine.category')
            ->whereNull('deleted_at')
            ->where('expiry_status', 'Expired')
            ->whereBetween('updated_at', [$windowStart, $windowEnd])
            ->get();

        // if ($outOfStock->isEmpty() && $lowStock->isEmpty() && $expiringSoon->isEmpty() && $expired->isEmpty()) {
        //     $this->info('Daily digest skipped — nothing new to report in the last 24 hours.');
        //     return;
        // }

        // ── Collect all recipient emails ───────────────────────────
        $emailList = $recipients->pluck('email')->toArray();

        if (empty($emailList)) return;

        // ── Send ONE digest to all recipients ──────────────────────
        try {
            $primaryUser  = $recipients->first();
            $primaryEmail = array_shift($emailList);

            $mailer = Mail::to($primaryEmail);

            foreach ($emailList as $email) {
                $mailer->bcc($email);
            }

            $mailer->send(new InventoryDailyDigestMail(
                $primaryUser,
                $outOfStock,
                $lowStock,
                $expiringSoon,
                $expired,
                $windowStart,
                $windowEnd
            ));

            $this->info("Daily digest sent to " . $recipients->count() . " recipient(s).");
        } catch (\Exception $e) {
            Log::error("InventoryDailyDigestMail failed: " . $e->getMessage());
            $this->error("Digest mail failed: " . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────────────────────

    private function buildTitle(string $alertType): string
    {
        return match ($alertType) {
            'expired'       => '⛔ Expired Medicine Alert',
            'expiring_soon' => '🟠 Expiring Soon Alert',
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
}
<?php

namespace App\Observers;

use App\Models\Medicine;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\InventoryAlertMail;

class MedicineObserver
{
    // ─────────────────────────────────────────────────────────────
    // How long (minutes) before the same alert can email again.
    // 1440 = 24 hours. Matches the cooldown in MedicineBatches.php.
    // Set to 0 to disable email cooldown entirely (not recommended).
    // ─────────────────────────────────────────────────────────────
    private const EMAIL_COOLDOWN_MINUTES = 1440;

    public function updated(Medicine $medicine): void
    {
        if (! $medicine->isDirty('stock')) return;

        $newStock  = $medicine->stock;
        $alertType = null;

        if ($newStock <= 0) {
            $alertType = 'out_of_stock';
        } elseif ($newStock <= 10) {
            $alertType = 'low_stock';
        }

        if ($alertType === null) return;

        $recipients = User::whereIn('role', ['nurse', 'staff'])->get();
        if ($recipients->isEmpty()) return;

        $this->notifyAll($recipients, $alertType, $medicine);
    }

    // ─────────────────────────────────────────────────────────────
    private function notifyAll($recipients, string $alertType, Medicine $medicine): void
    {
        $title = $this->buildTitle($alertType);

        // ── Stable bell message — NO stock count in the key ────────
        // This is the string stored in notifications.message and used
        // as the bell dedup key. Keeping it stock-count-free means
        // "already notified today" works correctly across dispenses.
        $bellMessage = $this->buildBellMessage($alertType, $medicine);

        // ── Stable email reference key — same logic ─────────────────
        // Used as the reference in inventory_email_logs so the 24-hour
        // cooldown is keyed on alert type + medicine identity, not on
        // a snapshot of the current stock number.
        $emailReference = md5($alertType . '|' . $medicine->medicine_id);

        foreach ($recipients as $user) {

            // ──────────────────────────────────────────────────────
            // BELL — dedup: one bell per user per alert type per day
            // ──────────────────────────────────────────────────────
            $bellAlreadySent = DB::table('notifications')
                ->where('user_id',          $user->id)
                ->where('type',             $alertType)
                ->where('appointment_type', 'inventory')
                ->where('message',          $bellMessage)
                ->whereDate('created_at',   today())
                ->exists();

            if (! $bellAlreadySent) {
                DB::table('notifications')->insert([
                    'user_id'          => $user->id,
                    'type'             => $alertType,
                    'title'            => $title,
                    'message'          => $bellMessage,
                    'appointment_type' => 'inventory',
                    'link_url'         => '/medicines?filter=' . $alertType,
                    'is_read'          => 0,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);
            }

            // ──────────────────────────────────────────────────────
            // EMAIL — checked independently from bell so a bell that
            // already fired today doesn't block the first email send.
            // Uses inventory_email_logs with the same 24-hour cooldown
            // pattern as MedicineBatches and CheckInventoryExpiry.
            // ──────────────────────────────────────────────────────
            if (self::EMAIL_COOLDOWN_MINUTES > 0) {
                $emailAlreadySent = DB::table('inventory_email_logs')
                    ->where('user_id',    $user->id)
                    ->where('alert_type', $alertType)
                    ->where('reference',  $emailReference)
                    ->where('sent_at', '>=', now()->subMinutes(self::EMAIL_COOLDOWN_MINUTES))
                    ->exists();

                if ($emailAlreadySent) continue;
            }

            try {
                // Mail::send() — synchronous, Hostinger-compatible.
                // Each user gets their own named email (no BCC batch),
                // so the greeting always shows the correct recipient name.
                Mail::to($user->email)->send(new InventoryAlertMail(
                    $alertType,
                    $medicine,
                    null,   // batchNumber — N/A for stock alerts
                    null,   // expiryDate  — N/A for stock alerts
                    $user   // ← each user gets their own greeting
                ));

                DB::table('inventory_email_logs')->insert([
                    'user_id'    => $user->id,
                    'alert_type' => $alertType,
                    'reference'  => $emailReference,
                    'sent_at'    => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Exception $e) {
                Log::error("InventoryAlertMail failed for {$user->email} (medicine #{$medicine->medicine_id}): " . $e->getMessage());
            }
        }
    }

    // ─────────────────────────────────────────────────────────────
    // BUILDERS
    // ─────────────────────────────────────────────────────────────

    private function buildTitle(string $alertType): string
    {
        return match ($alertType) {
            'out_of_stock' => '🔴 Out of Stock Alert',
            'low_stock'    => '🟡 Low Stock Alert',
            default        => '⚠️ Inventory Alert',
        };
    }

    // ── Stable bell message — stock count intentionally excluded ──
    // Including the count (e.g. "only 8 unit(s) left") causes a new
    // unique string on every dispense, defeating daily dedup entirely.
    private function buildBellMessage(string $alertType, Medicine $medicine): string
    {
        return match ($alertType) {
            'out_of_stock' => "{$medicine->medicine_name} ({$medicine->dosage}) is OUT OF STOCK.",
            'low_stock'    => "{$medicine->medicine_name} ({$medicine->dosage}) is running LOW on stock.",
            default        => "Inventory alert for {$medicine->medicine_name}.",
        };
    }
}
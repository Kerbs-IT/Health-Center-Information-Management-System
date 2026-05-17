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
    /**
     * Fires every time a Medicine record is saved.
     * Handles: Out of Stock, Low Stock (bell + ONE batch email to all recipients).
     * Expiry alerts are handled by CheckInventoryExpiry (scheduled command).
     */
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
    // INTERNAL HELPERS
    // ─────────────────────────────────────────────────────────────

    /**
     * Insert bell notifications for all recipients, then send ONE batch email.
     */
    private function notifyAll($recipients, string $alertType, Medicine $medicine): void
    {
        $title   = $this->buildTitle($alertType);
        $message = $this->buildMessage($alertType, $medicine);

        $emailRecipients = [];

        foreach ($recipients as $user) {
            // ── Deduplication ──────────────────────────────────────
            $alreadySent = DB::table('notifications')
                ->where('user_id',          $user->id)
                ->where('type',             $alertType)
                ->where('appointment_type', 'inventory')
                ->where('message',          $message)
                ->whereDate('created_at',   today())
                ->exists();

            if ($alreadySent) continue;

            // ── Bell notification ──────────────────────────────────
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

        if (empty($emailRecipients)) return;

        // ── ONE batch email to all qualifying recipients ────────────
        try {
            $primary = array_shift($emailRecipients); // To: field

            $mailer = Mail::to($primary->email);

            // Rest go as BCC — keeps recipient list private
            foreach ($emailRecipients as $bccUser) {
                $mailer->bcc($bccUser->email);
            }

            $mailer->send(new InventoryAlertMail(
                $alertType,
                $medicine,
                null,    // batchNumber — N/A for stock alerts
                null,    // expiryDate  — N/A for stock alerts
                $primary
            ));
        } catch (\Exception $e) {
            Log::error("InventoryAlertMail batch send failed for medicine #{$medicine->id}: " . $e->getMessage());
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

    private function buildMessage(string $alertType, Medicine $medicine): string
    {
        return match ($alertType) {
            'out_of_stock' => "{$medicine->medicine_name} ({$medicine->dosage}) is OUT OF STOCK.",
            'low_stock'    => "{$medicine->medicine_name} ({$medicine->dosage}) is running LOW — only {$medicine->stock} unit(s) left.",
            default        => "Inventory alert for {$medicine->medicine_name}.",
        };
    }
}
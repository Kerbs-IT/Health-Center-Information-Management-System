<?php
// app/Notifications/InventoryAlertNotification.php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Medicine;

class InventoryAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public string $alertType;
    public Medicine $medicine;
    public ?string $batchNumber;
    public ?string $expiryDate;

    public function __construct(string $alertType, Medicine $medicine, ?string $batchNumber = null, ?string $expiryDate = null)
    {
        $this->alertType   = $alertType;
        $this->medicine    = $medicine;
        $this->batchNumber = $batchNumber;
        $this->expiryDate  = $expiryDate;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    // ── Bell (database) ───────────────────────────────────────────
    public function toDatabase(object $notifiable): array
    {
        return [
            'alert_type'   => $this->alertType,
            'medicine_id'  => $this->medicine->medicine_id,
            'medicine_name'=> $this->medicine->medicine_name,
            'dosage'       => $this->medicine->dosage,
            'batch_number' => $this->batchNumber,
            'expiry_date'  => $this->expiryDate,
            'stock'        => $this->medicine->stock,
            'message'      => $this->buildMessage(),
            // tell the frontend this came from inventory (not appointment)
            'appointment_type' => 'inventory',
            'title'        => $this->buildTitle(),
            'link_url'     => '/medicines',
        ];
    }

    // ── Email ─────────────────────────────────────────────────────
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('⚠️ Inventory Alert: ' . $this->medicine->medicine_name)
            ->view('emails.inventory-alert', [
                'alertType'   => $this->alertType,
                'medicine'    => $this->medicine,
                'batchNumber' => $this->batchNumber,
                'expiryDate'  => $this->expiryDate,
                'message'     => $this->buildMessage(),
                'title'       => $this->buildTitle(),
                'notifiable'  => $notifiable,
            ]);
    }

    public function buildTitle(): string
    {
        return match($this->alertType) {
            'out_of_stock'  => '🔴 Out of Stock Alert',
            'low_stock'     => '🟡 Low Stock Alert',
            'expiring_soon' => '🟠 Expiring Soon Alert',
            'expired'       => '⛔ Expired Medicine Alert',
            default         => '⚠️ Inventory Alert',
        };
    }

public function buildMessage(): string
    {
        return match($this->alertType) {
            'out_of_stock'  => "{$this->medicine->medicine_name} ({$this->medicine->dosage}) is OUT OF STOCK.",
            'low_stock'     => "{$this->medicine->medicine_name} ({$this->medicine->dosage}) is running LOW — only {$this->medicine->stock} unit(s) left.",
            'expiring_soon' => "{$this->medicine->medicine_name} ({$this->medicine->dosage}) batch {$this->batchNumber} is expiring on {$this->expiryDate}.",
            'expired'       => "{$this->medicine->medicine_name} ({$this->medicine->dosage}) batch {$this->batchNumber} has EXPIRED as of {$this->expiryDate}.",
            default         => "Inventory alert for {$this->medicine->medicine_name}.",
        };
    }
}
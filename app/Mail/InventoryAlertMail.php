<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Medicine;
use App\Models\User;

class InventoryAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public string  $alertType;
    public string  $title;
    public Medicine $medicine;
    public ?string $batchNumber;
    public ?string $expiryDate;
    public User    $recipient;

    public function __construct(
        string   $alertType,
        Medicine $medicine,
        ?string  $batchNumber,
        ?string  $expiryDate,
        User     $recipient
    ) {
        $this->alertType   = $alertType;
        $this->medicine    = $medicine;
        $this->batchNumber = $batchNumber;
        $this->expiryDate  = $expiryDate;
        $this->recipient   = $recipient;

        // ← THIS WAS MISSING — Blade needs $title
        $this->title = match($alertType) {
            'out_of_stock'  => 'Out of Stock Alert',
            'low_stock'     => 'Low Stock Alert',
            'expiring_soon' => 'Expiring Soon Alert',
            'expired'       => 'Expired Medicine Alert',
            default         => 'Inventory Alert',
        };
    }

    public function build(): self
    {
        $subject = match($this->alertType) {
            'out_of_stock'  => '🔴 Out of Stock: ' . $this->medicine->medicine_name,
            'low_stock'     => '🟡 Low Stock: ' . $this->medicine->medicine_name,
            'expiring_soon' => '🟠 Expiring Soon: ' . $this->medicine->medicine_name,
            'expired'       => '⛔ Expired: ' . $this->medicine->medicine_name,
            default         => '⚠️ Inventory Alert: ' . $this->medicine->medicine_name,
        };

        return $this->subject($subject)
                    ->view('emails.inventory-alert');
    }
}
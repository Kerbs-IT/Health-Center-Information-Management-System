<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class InventoryDailyDigestMail extends Mailable
{
    use Queueable, SerializesModels;

    public User       $recipient;
    public Collection $outOfStock;
    public Collection $lowStock;
    public Collection $expiringSoon;
    public Collection $expired;
    public Carbon     $windowStart;
    public Carbon     $windowEnd;

    public function __construct(
        User       $recipient,
        Collection $outOfStock,
        Collection $lowStock,
        Collection $expiringSoon,
        Collection $expired,
        Carbon     $windowStart,
        Carbon     $windowEnd
    ) {
        $this->recipient    = $recipient;
        $this->outOfStock   = $outOfStock;
        $this->lowStock     = $lowStock;
        $this->expiringSoon = $expiringSoon;
        $this->expired      = $expired;
        $this->windowStart  = $windowStart;
        $this->windowEnd    = $windowEnd;
    }

    public function build(): self
    {
        $totalIssues = $this->outOfStock->count()
            + $this->lowStock->count()
            + $this->expiringSoon->count()
            + $this->expired->count();

        $subject = $totalIssues > 0
            ? "📋 Daily Inventory Report — {$totalIssues} item(s) flagged · " . now()->format('M d, Y')
            : "📋 Daily Inventory Report — ✅ All Clear · " . now()->format('M d, Y');

        return $this
            ->subject($subject)
            ->view('emails.inventory-daily-digest');
    }
}
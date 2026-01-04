<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OverdueAppointmentsMail extends Mailable
{
    use Queueable, SerializesModels;

    public $emailData;

    /**
     * Create a new message instance.
     */
    public function __construct($emailData)
    {
        $this->emailData = $emailData;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $subject = "⚠️ Overdue Appointments Alert - Action Required";

        return $this->subject($subject)
            ->view('emails.overdue-appointments')
            ->with('data', $this->emailData);
    }
}

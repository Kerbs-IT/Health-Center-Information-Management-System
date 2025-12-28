<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AppointmentReminderMail extends Mailable
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
        $subject = 'Appointment Reminder - ' . $this->emailData['appointment_type'] . ' Tomorrow';

        return $this->subject($subject)
            ->view('emails.appointment-reminder')
            ->with('data', $this->emailData);
    }
}

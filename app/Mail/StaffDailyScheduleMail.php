<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StaffDailyScheduleMail extends Mailable
{

    use Queueable,SerializesModels;

    public $emailData;

    public function __construct($emailData)
    {
        $this-> emailData = $emailData;

    }

    public function build(){
        $subject = "Your Schedule for Today - {$this->emailData['date']}";

        return $this->subject($subject)
                ->view('emails.staff-daily-schedule')
                ->with('data',$this->emailData);
    }
}
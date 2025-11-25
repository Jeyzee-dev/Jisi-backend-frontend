<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $subject;
    public $message;
    public $type;

    public function __construct(User $user, $subject, $message, $type = 'general')
    {
        $this->user = $user;
        $this->subject = $subject;
        $this->message = $message;
        $this->type = $type;
    }

    public function build()
    {
        $typeLabels = [
            'general' => 'General Message',
            'appointment' => 'Appointment Related',
            'notification' => 'System Notification',
            'urgent' => 'Urgent Message'
        ];

        return $this->subject($this->subject)
                    ->view('emails.admin-message')
                    ->with([
                        'user' => $this->user,
                        'messageContent' => $this->message,
                        'typeLabel' => $typeLabels[$this->type] ?? 'General Message'
                    ]);
    }
}
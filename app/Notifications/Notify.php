<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class Notify extends Notification
{
    use Queueable;

    protected $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    // Define how the notification will be stored in the database
    public function via($notifiable)
    {
        return ['database'];
    }

    // Define the content of the notification in the database
    public function toDatabase($notifiable)
    {
        return [
            'message' => $this->message,
            'created_at' => now(),
        ];
    }
}

<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class NewUserNotification extends Notification
{
    use Queueable;

    private $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    // Define channels
    public function via($notifiable)
    {
        return ['database'];
    }

    // Database payload
    public function toDatabase($notifiable)
    {
        return [
            'message' => $this->message,
            'user_id' => $notifiable->id,
        ];
    }
}

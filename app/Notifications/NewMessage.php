<?php

namespace App\Notifications;

use App\Backend\Models\Conversation;
use App\Backend\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewMessage extends Notification
{
    use Queueable;

    public function __construct(public User $sender, public Conversation $conversation) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'user_id' => $this->sender->id,
            'conversation_id' => $this->conversation->id,
            'message' => '@' . $this->sender->username . ' sent you a message',
        ];
    }
}

<?php

namespace App\Notifications;

use App\Backend\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewFollower extends Notification
{
    use Queueable;

    public function __construct(public User $follower) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'user_id' => $this->follower->id,
            'message' => '@' . $this->follower->username . ' started following you',
        ];
    }
}

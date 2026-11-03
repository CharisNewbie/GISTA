<?php

namespace App\Notifications;

use App\Backend\Models\Post;
use App\Backend\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PostLiked extends Notification
{
    use Queueable;

    public function __construct(public User $liker, public Post $post) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'user_id' => $this->liker->id,
            'post_id' => $this->post->id,
            'message' => '@' . $this->liker->username . ' liked your post',
        ];
    }
}

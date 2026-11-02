<?php

namespace App\Notifications;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PostCommented extends Notification
{
    use Queueable;

    public function __construct(public User $commenter, public Post $post, public Comment $comment) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'user_id' => $this->commenter->id,
            'post_id' => $this->post->id,
            'comment_id' => $this->comment->id,
            'message' => '@' . $this->commenter->username . ' commented on your post',
        ];
    }
}

<?php

namespace App\Notifications;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Support\NotificationType;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class PostCommentedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly User $commenter,
        public readonly Post $post,
        public readonly Comment $comment,
    ) {
    }

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => NotificationType::POST_COMMENTED,
            'post_id' => $this->post->id,
            'post_title' => $this->post->title,
            'post_slug' => $this->post->slug,
            'actor_id' => $this->commenter->id,
            'actor_name' => $this->commenter->name,
            'comment_id' => $this->comment->id,
            'comment_excerpt' => Str::limit($this->comment->body, 120),
        ];
    }
}

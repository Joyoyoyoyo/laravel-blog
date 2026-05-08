<?php

namespace App\Notifications;

use App\Models\Post;
use App\Models\User;
use App\Support\NotificationType;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PostLikedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly User $liker,
        public readonly Post $post,
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
            'type' => NotificationType::POST_LIKED,
            'post_id' => $this->post->id,
            'post_title' => $this->post->title,
            'post_slug' => $this->post->slug,
            'actor_id' => $this->liker->id,
            'actor_name' => $this->liker->name,
        ];
    }
}

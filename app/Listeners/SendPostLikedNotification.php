<?php

namespace App\Listeners;

use App\Events\PostLiked;
use App\Notifications\PostLikedNotification;

class SendPostLikedNotification
{
    public function handle(PostLiked $event): void
    {
        $post = $event->post;
        $liker = $event->liker;

        if ($post->user_id === $liker->id) {
            return;
        }

        $post->user?->notify(new PostLikedNotification($liker, $post));
    }
}

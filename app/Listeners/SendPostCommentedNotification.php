<?php

namespace App\Listeners;

use App\Events\PostCommented;
use App\Notifications\PostCommentedNotification;

class SendPostCommentedNotification
{
    public function handle(PostCommented $event): void
    {
        $post = $event->post;
        $commenter = $event->commenter;

        if ($post->user_id === $commenter->id) {
            return;
        }

        $post->user?->notify(new PostCommentedNotification($commenter, $post, $event->comment));
    }
}

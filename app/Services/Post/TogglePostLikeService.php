<?php

namespace App\Services\Post;

use App\Models\Post;
use App\Models\User;
use App\Notifications\PostLikedNotification;

class TogglePostLikeService
{
    /**
     * @return array{liked_by_auth_user: bool, likes_count: int}
     */
    public function toggle(User $user, Post $post): array
    {
        $existingLike = $post->likes()
            ->where('user_id', $user->id)
            ->first();

        if ($existingLike) {
            $existingLike->delete();

            return [
                'liked_by_auth_user' => false,
                'likes_count' => $post->likes()->count(),
            ];
        }

        $post->likes()->create([
            'user_id' => $user->id,
        ]);

        $this->notifyPostAuthor($user, $post);

        return [
            'liked_by_auth_user' => true,
            'likes_count' => $post->likes()->count(),
        ];
    }

    private function notifyPostAuthor(User $liker, Post $post): void
    {
        if ($post->user_id === $liker->id) {
            return;
        }

        $post->user?->notify(new PostLikedNotification($liker, $post));
    }
}

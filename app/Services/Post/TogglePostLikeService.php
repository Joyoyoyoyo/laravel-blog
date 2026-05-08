<?php

namespace App\Services\Post;

use App\Events\PostLiked;
use App\Models\Post;
use App\Models\User;

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

        PostLiked::dispatch($user, $post);

        return [
            'liked_by_auth_user' => true,
            'likes_count' => $post->likes()->count(),
        ];
    }
}

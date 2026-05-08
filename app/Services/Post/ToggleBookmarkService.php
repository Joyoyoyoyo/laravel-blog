<?php

namespace App\Services\Post;

use App\Models\Post;
use App\Models\User;

class ToggleBookmarkService
{
    /**
     * @return array{bookmarked_by_auth_user: bool}
     */
    public function toggle(User $user, Post $post): array
    {
        $existing = $post->bookmarks()
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            $existing->delete();

            return ['bookmarked_by_auth_user' => false];
        }

        $post->bookmarks()->create([
            'user_id' => $user->id,
        ]);

        return ['bookmarked_by_auth_user' => true];
    }
}

<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Support\Carbon;

class CommentPolicy
{
    public function update(User $user, Comment $comment): bool
    {
        if ($user->id !== $comment->user_id) {
            return false;
        }

        if ($comment->created_at === null) {
            return false;
        }

        return $comment->created_at->greaterThanOrEqualTo(Carbon::now()->subMinutes(15));
    }
}

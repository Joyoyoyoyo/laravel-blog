<?php

namespace App\Services\Comment;

use App\Events\PostCommented;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class UpsertCommentService
{
    /**
     * @param  array{id?: int|null, post_id: int, body: string}  $data
     */
    public function upsert(User $user, array $data): Comment
    {
        $commentId = $data['id'] ?? null;

        if ($commentId !== null) {
            $comment = Comment::query()->findOrFail($commentId);
            Gate::authorize('manage-comment', $comment);
            $comment->body = $data['body'];
            $comment->save();

            return $comment;
        }

        $comment = Comment::create([
            'user_id' => $user->id,
            'post_id' => $data['post_id'],
            'body' => $data['body'],
        ]);

        $comment->load('post');

        if ($comment->post !== null) {
            PostCommented::dispatch($user, $comment->post, $comment);
        }

        return $comment;
    }
}

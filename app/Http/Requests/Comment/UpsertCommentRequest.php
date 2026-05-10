<?php

namespace App\Http\Requests\Comment;

use App\Models\Comment;
use Illuminate\Foundation\Http\FormRequest;

class UpsertCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        if ($user === null) {
            return false;
        }

        $commentId = $this->input('id');
        if ($commentId === null || $commentId === '') {
            return true;
        }

        $comment = Comment::query()->find((int) $commentId);

        return $comment !== null && $user->can('manage-comment', $comment);
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'id' => ['nullable', 'integer', 'exists:comments,id'],
            'post_id' => ['required', 'integer', 'exists:posts,id'],
            'body' => ['required', 'string', 'max:2000'],
        ];
    }
}

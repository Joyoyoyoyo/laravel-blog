<?php

namespace App\Http\Requests\Comment;

use Illuminate\Foundation\Http\FormRequest;

class UpsertCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
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

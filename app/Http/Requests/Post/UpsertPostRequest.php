<?php

namespace App\Http\Requests\Post;

use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;

class UpsertPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        if ($user === null) {
            return false;
        }

        $post = $this->route('post');

        return ! ($post instanceof Post) || $user->can('manage-post', $post);
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
        ];
    }
}

<?php

namespace App\Http\Resources;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;

/**
 * @mixin Comment
 */
class CommentResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $authUser = $request->user();

        return [
            'id' => $this->id,
            'body' => $this->body,
            'author' => $this->user?->name,
            'author_id' => $this->user_id,
            'created_at' => $this->created_at?->toDateTimeString(),
            'can_edit' => $authUser !== null
                ? Gate::forUser($authUser)->allows('manage-comment', $this->resource)
                : false,
        ];
    }
}

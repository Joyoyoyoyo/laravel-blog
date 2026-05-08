<?php

namespace App\Http\Resources;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Post
 */
class PostResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'body' => $this->body,
            'slug' => $this->slug,
            'created_at' => $this->created_at?->toDateTimeString(),
            'author' => $this->user?->name,
            'author_id' => $this->user_id,
            'category' => $this->category?->name,
            'likes_count' => $this->likes_count,
            'liked_by_auth_user' => $this->likes->isNotEmpty(),
            'bookmarked_by_auth_user' => $this->relationLoaded('bookmarks')
                ? $this->bookmarks->isNotEmpty()
                : false,
            'comments' => CommentResource::collection($this->comments)->resolve($request),
        ];
    }
}

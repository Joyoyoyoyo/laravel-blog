<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
class UserProfileResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'bio' => $this->bio,
            'member_since' => $this->created_at?->toDateString(),
            'posts_count' => $this->whenCounted('posts'),
            'likes_received_count' => $this->whenCounted('likesReceived'),
            'subscribers_count' => $this->whenCounted('subscribers'),
            'subscribed_by_auth_user' => $this->when(
                $request->user() !== null && ! $request->user()->is($this->resource),
                fn (): bool => $request->user()->subscribedUsers()->whereKey($this->getKey())->exists(),
            ),
        ];
    }
}

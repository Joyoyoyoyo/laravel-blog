<?php

namespace App\Services\Post;

use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PostFeedService
{
    /**
     * Charge, pagine et transforme un feed de posts.
     *
     * @param  Builder<Post>|HasMany<Post>  $base  Query deja filtree.
     */
    public function paginate(Builder|HasMany $base, ?User $authUser, int $perPage = 10): LengthAwarePaginator
    {
        return $base
            ->with([
                'user:id,name',
                'category:id,name',
                'comments' => fn ($query) => $query
                    ->with('user:id,name')
                    ->oldest(),
                'likes' => fn ($query) => $query
                    ->select('id', 'post_id', 'user_id')
                    ->when(
                        $authUser !== null,
                        fn ($likesQuery) => $likesQuery->where('user_id', $authUser->id),
                        fn ($likesQuery) => $likesQuery->whereRaw('1 = 0'),
                    ),
            ])
            ->withCount('likes')
            ->latest()
            ->paginate($perPage)
            ->withQueryString()
            ->through(fn (Post $post) => PostResource::make($post)->resolve(request()));
    }
}

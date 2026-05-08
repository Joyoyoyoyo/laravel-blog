<?php

namespace App\Services\Post;

use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Str;

class UpsertPostService
{
    /**
     * @param  array{title: string, body: string, category_id: int}  $data
     */
    public function create(User $user, array $data): Post
    {
        $post = new Post();
        $post->user_id = $user->id;

        return $this->persist($post, $data);
    }

    /**
     * @param  array{title: string, body: string, category_id: int}  $data
     */
    public function update(Post $post, array $data): Post
    {
        return $this->persist($post, $data);
    }

    /**
     * @param  array{title: string, body: string, category_id: int}  $data
     */
    private function persist(Post $post, array $data): Post
    {
        $post->title = $data['title'];
        $post->body = $data['body'];
        $post->category_id = $data['category_id'];
        $post->slug = $this->uniqueSlug($data['title'], $post->id);
        $post->save();

        return $post;
    }

    private function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($title);
        $baseSlug = $baseSlug !== '' ? $baseSlug : 'post';
        $slug = $baseSlug;
        $count = 1;

        while (
            Post::query()
                ->where('slug', $slug)
                ->when($ignoreId !== null, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $baseSlug.'-'.$count;
            $count++;
        }

        return $slug;
    }
}

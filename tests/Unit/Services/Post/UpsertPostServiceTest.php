<?php

namespace Tests\Unit\Services\Post;

use App\Models\Category;
use App\Models\User;
use App\Services\Post\UpsertPostService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpsertPostServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_post_with_a_slug_from_title(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $post = app(UpsertPostService::class)->create($user, [
            'title' => 'Hello World',
            'body' => 'My body',
            'category_id' => $category->id,
        ]);

        $this->assertSame('hello-world', $post->slug);
        $this->assertSame($user->id, $post->user_id);
        $this->assertSame($category->id, $post->category_id);
    }

    public function test_duplicate_titles_get_incremental_slugs(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $service = app(UpsertPostService::class);

        $first = $service->create($user, [
            'title' => 'Hello World',
            'body' => 'b1',
            'category_id' => $category->id,
        ]);
        $second = $service->create($user, [
            'title' => 'Hello World',
            'body' => 'b2',
            'category_id' => $category->id,
        ]);
        $third = $service->create($user, [
            'title' => 'Hello World',
            'body' => 'b3',
            'category_id' => $category->id,
        ]);

        $this->assertSame('hello-world', $first->slug);
        $this->assertSame('hello-world-1', $second->slug);
        $this->assertSame('hello-world-2', $third->slug);
    }

    public function test_empty_title_falls_back_to_post_slug(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $post = app(UpsertPostService::class)->create($user, [
            'title' => '!!!',
            'body' => 'body',
            'category_id' => $category->id,
        ]);

        $this->assertSame('post', $post->slug);
    }

    public function test_updating_a_post_with_same_title_keeps_its_slug(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $service = app(UpsertPostService::class);

        $post = $service->create($user, [
            'title' => 'Hello World',
            'body' => 'b1',
            'category_id' => $category->id,
        ]);

        $updated = $service->update($post, [
            'title' => 'Hello World',
            'body' => 'b1 updated',
            'category_id' => $category->id,
        ]);

        $this->assertSame('hello-world', $updated->slug);
        $this->assertSame('b1 updated', $updated->body);
    }
}

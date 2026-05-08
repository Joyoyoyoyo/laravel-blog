<?php

namespace Tests\Unit\Services\Post;

use App\Models\Post;
use App\Models\User;
use App\Services\Post\ToggleBookmarkService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ToggleBookmarkServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_bookmark(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $result = app(ToggleBookmarkService::class)->toggle($user, $post);

        $this->assertTrue($result['bookmarked_by_auth_user']);
        $this->assertDatabaseHas('bookmarks', [
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);
    }

    public function test_it_removes_an_existing_bookmark(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();
        $post->bookmarks()->create(['user_id' => $user->id]);

        $result = app(ToggleBookmarkService::class)->toggle($user, $post);

        $this->assertFalse($result['bookmarked_by_auth_user']);
        $this->assertDatabaseMissing('bookmarks', [
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);
    }

    public function test_it_does_not_affect_other_users_bookmarks(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $post = Post::factory()->create();
        $post->bookmarks()->create(['user_id' => $userB->id]);

        $result = app(ToggleBookmarkService::class)->toggle($userA, $post);

        $this->assertTrue($result['bookmarked_by_auth_user']);
        $this->assertDatabaseHas('bookmarks', [
            'user_id' => $userA->id,
            'post_id' => $post->id,
        ]);
        $this->assertDatabaseHas('bookmarks', [
            'user_id' => $userB->id,
            'post_id' => $post->id,
        ]);
    }
}

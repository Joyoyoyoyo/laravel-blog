<?php

namespace Tests\Feature\Api\Post;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiToggleBookmarkControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_bookmark(): void
    {
        $post = Post::factory()->create();

        $this->postJson("/api/posts/{$post->id}/bookmark")
            ->assertUnauthorized();
    }

    public function test_authenticated_user_can_bookmark_a_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $this->actingAs($user)
            ->postJson("/api/posts/{$post->id}/bookmark")
            ->assertOk()
            ->assertJsonPath('data.post_id', $post->id)
            ->assertJsonPath('data.bookmarked_by_auth_user', true);

        $this->assertDatabaseHas('bookmarks', [
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);
    }

    public function test_toggling_twice_removes_the_bookmark(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $this->actingAs($user)
            ->postJson("/api/posts/{$post->id}/bookmark")
            ->assertOk();

        $this->actingAs($user)
            ->postJson("/api/posts/{$post->id}/bookmark")
            ->assertOk()
            ->assertJsonPath('data.bookmarked_by_auth_user', false);

        $this->assertDatabaseMissing('bookmarks', [
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);
    }

    public function test_returns_404_for_unknown_post(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson('/api/posts/999999/bookmark')
            ->assertNotFound();
    }
}

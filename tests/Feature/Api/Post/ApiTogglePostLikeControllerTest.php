<?php

namespace Tests\Feature\Api\Post;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTogglePostLikeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_like(): void
    {
        $post = Post::factory()->create();

        $this->postJson("/api/posts/{$post->id}/like")
            ->assertUnauthorized();
    }

    public function test_authenticated_user_can_like_a_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $this->actingAs($user)
            ->postJson("/api/posts/{$post->id}/like")
            ->assertOk()
            ->assertJsonPath('data.post_id', $post->id)
            ->assertJsonPath('data.liked_by_auth_user', true)
            ->assertJsonPath('data.likes_count', 1);

        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);
    }

    public function test_toggling_twice_unlikes_the_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $this->actingAs($user)
            ->postJson("/api/posts/{$post->id}/like")
            ->assertOk();

        $this->actingAs($user)
            ->postJson("/api/posts/{$post->id}/like")
            ->assertOk()
            ->assertJsonPath('data.liked_by_auth_user', false)
            ->assertJsonPath('data.likes_count', 0);

        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);
    }

    public function test_returns_404_for_unknown_post(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson('/api/posts/999999/like')
            ->assertNotFound();
    }
}

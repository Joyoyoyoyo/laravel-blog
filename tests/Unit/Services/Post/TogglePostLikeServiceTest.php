<?php

namespace Tests\Unit\Services\Post;

use App\Events\PostLiked;
use App\Models\Post;
use App\Models\User;
use App\Services\Post\TogglePostLikeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class TogglePostLikeServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_like_and_dispatches_event(): void
    {
        Event::fake();

        $author = User::factory()->create();
        $liker = User::factory()->create();
        $post = Post::factory()->for($author, 'user')->create();

        $result = app(TogglePostLikeService::class)->toggle($liker, $post);

        $this->assertTrue($result['liked_by_auth_user']);
        $this->assertSame(1, $result['likes_count']);
        $this->assertDatabaseHas('likes', [
            'user_id' => $liker->id,
            'post_id' => $post->id,
        ]);
        Event::assertDispatched(
            PostLiked::class,
            fn (PostLiked $event) => $event->liker->is($liker) && $event->post->is($post),
        );
    }

    public function test_it_removes_an_existing_like_and_does_not_dispatch_event(): void
    {
        Event::fake();

        $liker = User::factory()->create();
        $post = Post::factory()->create();
        $post->likes()->create(['user_id' => $liker->id]);

        $result = app(TogglePostLikeService::class)->toggle($liker, $post);

        $this->assertFalse($result['liked_by_auth_user']);
        $this->assertSame(0, $result['likes_count']);
        $this->assertDatabaseMissing('likes', [
            'user_id' => $liker->id,
            'post_id' => $post->id,
        ]);
        Event::assertNotDispatched(PostLiked::class);
    }

    public function test_it_returns_correct_likes_count_when_others_already_liked(): void
    {
        Event::fake();

        $liker = User::factory()->create();
        $post = Post::factory()->create();
        $post->likes()->create(['user_id' => User::factory()->create()->id]);
        $post->likes()->create(['user_id' => User::factory()->create()->id]);

        $result = app(TogglePostLikeService::class)->toggle($liker, $post);

        $this->assertTrue($result['liked_by_auth_user']);
        $this->assertSame(3, $result['likes_count']);
    }
}

<?php

namespace Tests\Unit\Services\Comment;

use App\Events\PostCommented;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Services\Comment\UpsertCommentService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class UpsertCommentServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_comment_and_dispatches_event(): void
    {
        Event::fake();

        $author = User::factory()->create();
        $commenter = User::factory()->create();
        $post = Post::factory()->for($author, 'user')->create();

        $comment = app(UpsertCommentService::class)->upsert($commenter, [
            'post_id' => $post->id,
            'body' => 'Hello there',
        ]);

        $this->assertSame($commenter->id, $comment->user_id);
        $this->assertSame($post->id, $comment->post_id);
        $this->assertSame('Hello there', $comment->body);
        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'body' => 'Hello there',
        ]);
        Event::assertDispatched(
            PostCommented::class,
            fn (PostCommented $event) => $event->commenter->is($commenter)
                && $event->post->is($post)
                && $event->comment->is($comment),
        );
    }

    public function test_updating_a_comment_does_not_dispatch_event(): void
    {
        Event::fake();

        $owner = User::factory()->create();
        $post = Post::factory()->create();
        $comment = Comment::factory()->for($owner, 'user')->for($post, 'post')->create([
            'body' => 'Original',
        ]);

        $updated = app(UpsertCommentService::class)->upsert($owner, [
            'id' => $comment->id,
            'post_id' => $post->id,
            'body' => 'Edited body',
        ]);

        $this->assertSame($comment->id, $updated->id);
        $this->assertSame('Edited body', $updated->fresh()->body);
        Event::assertNotDispatched(PostCommented::class);
    }

    public function test_updating_a_comment_belonging_to_another_user_is_forbidden(): void
    {
        Event::fake();

        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $post = Post::factory()->create();
        $comment = Comment::factory()->for($owner, 'user')->for($post, 'post')->create([
            'body' => 'Original',
        ]);

        $this->expectException(AuthorizationException::class);

        app(UpsertCommentService::class)->upsert($intruder, [
            'id' => $comment->id,
            'post_id' => $post->id,
            'body' => 'Hacked',
        ]);
    }
}

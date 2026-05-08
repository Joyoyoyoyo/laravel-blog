<?php

namespace Tests\Unit\Listeners;

use App\Events\PostCommented;
use App\Listeners\SendPostCommentedNotification;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Notifications\PostCommentedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SendPostCommentedNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_notifies_post_author(): void
    {
        Notification::fake();

        $author = User::factory()->create();
        $commenter = User::factory()->create();
        $post = Post::factory()->for($author, 'user')->create();
        $comment = Comment::factory()->for($post, 'post')->for($commenter, 'user')->create();

        (new SendPostCommentedNotification())->handle(new PostCommented($commenter, $post, $comment));

        Notification::assertSentTo(
            $author,
            PostCommentedNotification::class,
            fn (PostCommentedNotification $notification) => $notification->commenter->is($commenter)
                && $notification->post->is($post)
                && $notification->comment->is($comment),
        );
    }

    public function test_it_does_not_notify_when_author_comments_their_own_post(): void
    {
        Notification::fake();

        $author = User::factory()->create();
        $post = Post::factory()->for($author, 'user')->create();
        $comment = Comment::factory()->for($post, 'post')->for($author, 'user')->create();

        (new SendPostCommentedNotification())->handle(new PostCommented($author, $post, $comment));

        Notification::assertNothingSent();
    }
}

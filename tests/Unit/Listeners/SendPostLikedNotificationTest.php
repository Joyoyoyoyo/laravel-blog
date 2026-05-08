<?php

namespace Tests\Unit\Listeners;

use App\Events\PostLiked;
use App\Listeners\SendPostLikedNotification;
use App\Models\Post;
use App\Models\User;
use App\Notifications\PostLikedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SendPostLikedNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_notifies_post_author(): void
    {
        Notification::fake();

        $author = User::factory()->create();
        $liker = User::factory()->create();
        $post = Post::factory()->for($author, 'user')->create();

        (new SendPostLikedNotification())->handle(new PostLiked($liker, $post));

        Notification::assertSentTo(
            $author,
            PostLikedNotification::class,
            fn (PostLikedNotification $notification) => $notification->liker->is($liker)
                && $notification->post->is($post),
        );
    }

    public function test_it_does_not_notify_when_author_likes_their_own_post(): void
    {
        Notification::fake();

        $author = User::factory()->create();
        $post = Post::factory()->for($author, 'user')->create();

        (new SendPostLikedNotification())->handle(new PostLiked($author, $post));

        Notification::assertNothingSent();
    }
}

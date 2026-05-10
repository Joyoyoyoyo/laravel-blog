<?php

namespace Tests\Unit\Services\User;

use App\Models\User;
use App\Services\User\ToggleUserSubscriptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class ToggleUserSubscriptionServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_cannot_subscribe_to_self(): void
    {
        $user = User::factory()->create();

        $this->expectException(InvalidArgumentException::class);

        app(ToggleUserSubscriptionService::class)->toggle($user, $user);
    }

    public function test_toggle_attaches_and_detaches(): void
    {
        $subscriber = User::factory()->create();
        $target = User::factory()->create();
        $service = app(ToggleUserSubscriptionService::class);

        $first = $service->toggle($subscriber, $target);
        $this->assertTrue($first['subscribed_by_auth_user']);
        $this->assertSame(1, $first['subscribers_count']);

        $second = $service->toggle($subscriber, $target);
        $this->assertFalse($second['subscribed_by_auth_user']);
        $this->assertSame(0, $second['subscribers_count']);
    }
}

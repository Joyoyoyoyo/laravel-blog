<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserSubscribersPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_subscribers_list(): void
    {
        $profile = User::factory()->create();
        $subscriber = User::factory()->create();
        $subscriber->subscribedUsers()->attach($profile);

        $response = $this->get("/users/{$profile->id}/subscribers");

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Users/Subscribers')
            ->where('profileUser.id', $profile->id)
            ->has('subscribers.data', 1)
            ->where('subscribers.data.0.id', $subscriber->id));
    }

    public function test_returns_404_for_unknown_user(): void
    {
        $this->get('/users/999999/subscribers')
            ->assertNotFound();
    }
}

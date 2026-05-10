<?php

namespace Tests\Feature\Api\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiToggleUserSubscriptionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_subscribe(): void
    {
        $user = User::factory()->create();

        $this->postJson("/api/users/{$user->id}/subscribe")
            ->assertUnauthorized();
    }

    public function test_authenticated_user_can_subscribe(): void
    {
        $auth = User::factory()->create();
        $profile = User::factory()->create();

        $this->actingAs($auth)
            ->postJson("/api/users/{$profile->id}/subscribe")
            ->assertOk()
            ->assertJsonPath('data.user_id', $profile->id)
            ->assertJsonPath('data.subscribed_by_auth_user', true)
            ->assertJsonPath('data.subscribers_count', 1);

        $this->assertDatabaseHas('user_subscriptions', [
            'subscriber_id' => $auth->id,
            'subscribed_user_id' => $profile->id,
        ]);
    }

    public function test_toggling_twice_unsubscribes(): void
    {
        $auth = User::factory()->create();
        $profile = User::factory()->create();

        $this->actingAs($auth)
            ->postJson("/api/users/{$profile->id}/subscribe")
            ->assertOk();

        $this->actingAs($auth)
            ->postJson("/api/users/{$profile->id}/subscribe")
            ->assertOk()
            ->assertJsonPath('data.subscribed_by_auth_user', false)
            ->assertJsonPath('data.subscribers_count', 0);

        $this->assertDatabaseMissing('user_subscriptions', [
            'subscriber_id' => $auth->id,
            'subscribed_user_id' => $profile->id,
        ]);
    }

    public function test_cannot_subscribe_to_own_profile(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson("/api/users/{$user->id}/subscribe")
            ->assertStatus(422)
            ->assertJsonFragment(['message' => 'Vous ne pouvez pas vous abonner à vous-même.']);
    }

    public function test_returns_404_for_unknown_user(): void
    {
        $auth = User::factory()->create();

        $this->actingAs($auth)
            ->postJson('/api/users/999999/subscribe')
            ->assertNotFound();
    }
}

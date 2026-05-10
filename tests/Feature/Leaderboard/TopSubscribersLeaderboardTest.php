<?php

namespace Tests\Feature\Leaderboard;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TopSubscribersLeaderboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_lists_users_ordered_by_subscribers_count_desc(): void
    {
        $popular = User::factory()->create(['name' => 'Popular']);
        $average = User::factory()->create(['name' => 'Average']);
        $quiet = User::factory()->create(['name' => 'Quiet']);

        User::factory()->count(5)->create()->each(fn (User $u) => $u->subscribedUsers()->attach($popular));
        User::factory()->count(2)->create()->each(fn (User $u) => $u->subscribedUsers()->attach($average));
        User::factory()->create()->subscribedUsers()->attach($quiet);

        $response = $this->get('/leaderboard/subscribers');

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Leaderboard/TopSubscribers')
            ->where('ranking.0.id', $popular->id)
            ->where('ranking.0.subscribers_count', 5)
            ->where('ranking.1.id', $average->id)
            ->where('ranking.1.subscribers_count', 2)
            ->where('ranking.2.id', $quiet->id)
            ->where('ranking.2.subscribers_count', 1));
    }

    public function test_guest_can_view_leaderboard(): void
    {
        User::factory()->create();

        $this->get('/leaderboard/subscribers')->assertOk();
    }
}

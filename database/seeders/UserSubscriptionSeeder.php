<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSubscriptionSeeder extends Seeder
{
    /**
     * Random subscriptions among existing users (subscribedUsers / subscribers pivot).
     */
    public function run(): void
    {
        $users = User::query()->get();

        $cap = $users->count() - 1;
        if ($cap < 1) {
            return;
        }

        $maxOutgoing = min(20, $cap);

        foreach ($users as $subscriber) {
            $others = $users->reject(fn (User $u): bool => $u->is($subscriber))->shuffle();
            $minOutgoing = min(3, $others->count());
            $take = fake()->numberBetween($minOutgoing, max($minOutgoing, $maxOutgoing));
            $take = min($take, $others->count());

            if ($take === 0) {
                continue;
            }

            $subscriber->subscribedUsers()->syncWithoutDetaching(
                $others->take($take)->pluck('id')->all()
            );
        }
    }
}

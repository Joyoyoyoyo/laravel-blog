<?php

namespace App\Services\User;

use App\Models\User;
use App\Support\TopSubscribersLeaderboardCache;
use InvalidArgumentException;

class ToggleUserSubscriptionService
{
    /**
     * @return array{subscribed_by_auth_user: bool, subscribers_count: int}
     */
    public function toggle(User $subscriber, User $target): array
    {
        if ($subscriber->is($target)) {
            throw new InvalidArgumentException('Vous ne pouvez pas vous abonner à vous-même.');
        }

        $relation = $subscriber->subscribedUsers();

        if ($relation->whereKey($target->getKey())->exists()) {
            $relation->detach($target->getKey());
            $subscribed = false;
        } else {
            $relation->attach($target->getKey());
            $subscribed = true;
        }

        TopSubscribersLeaderboardCache::forget();

        return [
            'subscribed_by_auth_user' => $subscribed,
            'subscribers_count' => $target->subscribers()->count(),
        ];
    }
}

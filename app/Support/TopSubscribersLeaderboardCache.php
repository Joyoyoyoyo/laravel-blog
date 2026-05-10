<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

final class TopSubscribersLeaderboardCache
{
    public const KEY = 'leaderboard.top_subscribers';

    public static function ttlSeconds(): int
    {
        return 300;
    }

    public static function remember(callable $resolver): mixed
    {
        return Cache::remember(self::KEY, now()->addSeconds(self::ttlSeconds()), $resolver);
    }

    public static function forget(): void
    {
        Cache::forget(self::KEY);
    }
}

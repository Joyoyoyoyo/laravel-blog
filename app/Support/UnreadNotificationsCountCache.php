<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

final class UnreadNotificationsCountCache
{
    private static function keyForUserId(int|string $userId): string
    {
        return 'users.'.$userId.'.unread_notifications_count';
    }

    public static function get(User $user): int
    {
        return (int) Cache::remember(
            self::keyForUserId($user->getKey()),
            now()->addDay(),
            fn () => $user->unreadNotifications()->count()
        );
    }

    public static function forget(User $user): void
    {
        Cache::forget(self::keyForUserId($user->getKey()));
    }

    public static function forgetByUserId(int|string $userId): void
    {
        Cache::forget(self::keyForUserId($userId));
    }
}

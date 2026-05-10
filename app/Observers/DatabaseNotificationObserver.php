<?php

namespace App\Observers;

use App\Models\User;
use App\Support\UnreadNotificationsCountCache;
use Illuminate\Notifications\DatabaseNotification;

class DatabaseNotificationObserver
{
    public function created(DatabaseNotification $notification): void
    {
        $this->forgetIfUser($notification);
    }

    public function updated(DatabaseNotification $notification): void
    {
        if ($notification->wasChanged('read_at')) {
            $this->forgetIfUser($notification);
        }
    }

    public function deleted(DatabaseNotification $notification): void
    {
        $this->forgetIfUser($notification);
    }

    private function forgetIfUser(DatabaseNotification $notification): void
    {
        if ($notification->notifiable_type !== User::class) {
            return;
        }

        UnreadNotificationsCountCache::forgetByUserId($notification->notifiable_id);
    }
}

<?php

namespace App\Http\Controllers\Api\Notification;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiMarkNotificationReadController extends Controller
{
    public function single(Request $request, string $notification): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $notification = $user->notifications()
            ->where('id', $notification)
            ->firstOrFail();
        $notification->markAsRead();

        return response()->json([
            'message' => 'Notification marked as read',
            'data' => [
                'id' => $notification->id,
                'unread_count' => $user->unreadNotifications()->count(),
            ],
        ]);
    }

    public function all(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $user->unreadNotifications->markAsRead();

        return response()->json([
            'message' => 'All notifications marked as read',
            'data' => [
                'unread_count' => 0,
            ],
        ]);
    }
}

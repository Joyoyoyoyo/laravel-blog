<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\User\ToggleUserSubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class ApiToggleUserSubscriptionController extends Controller
{
    public function __invoke(Request $request, User $user, ToggleUserSubscriptionService $toggleUserSubscriptionService): JsonResponse
    {
        /** @var User $auth */
        $auth = $request->user();

        try {
            $result = $toggleUserSubscriptionService->toggle($auth, $user);
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'message' => $result['subscribed_by_auth_user']
                ? 'Abonnement enregistré.'
                : 'Abonnement retiré.',
            'data' => [
                'user_id' => $user->id,
                'subscribed_by_auth_user' => $result['subscribed_by_auth_user'],
                'subscribers_count' => $result['subscribers_count'],
            ],
        ]);
    }
}

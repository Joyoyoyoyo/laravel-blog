<?php

namespace App\Http\Controllers\Api\Post;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use App\Services\Post\TogglePostLikeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiTogglePostLikeController extends Controller
{
    public function __invoke(Request $request, Post $post, TogglePostLikeService $togglePostLikeService): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $result = $togglePostLikeService->toggle($user, $post);

        return response()->json([
            'message' => $result['liked_by_auth_user'] ? 'Post liked successfully' : 'Post unliked successfully',
            'data' => [
                'post_id' => $post->id,
                'liked_by_auth_user' => $result['liked_by_auth_user'],
                'likes_count' => $result['likes_count'],
            ],
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api\Post;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use App\Services\Post\ToggleBookmarkService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiToggleBookmarkController extends Controller
{
    public function __invoke(Request $request, Post $post, ToggleBookmarkService $toggleBookmarkService): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $result = $toggleBookmarkService->toggle($user, $post);

        return response()->json([
            'message' => $result['bookmarked_by_auth_user']
                ? 'Post bookmarked successfully'
                : 'Post unbookmarked successfully',
            'data' => [
                'post_id' => $post->id,
                'bookmarked_by_auth_user' => $result['bookmarked_by_auth_user'],
            ],
        ]);
    }
}

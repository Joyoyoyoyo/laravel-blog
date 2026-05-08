<?php

namespace App\Http\Controllers\Api\Comment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Comment\UpsertCommentRequest;
use App\Models\User;
use App\Services\Comment\UpsertCommentService;
use Illuminate\Http\JsonResponse;

class ApiUpsertCommentController extends Controller
{
    public function __invoke(UpsertCommentRequest $request, UpsertCommentService $upsertCommentService): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $comment = $upsertCommentService->upsert($user, $request->validated());
        $isCreate = $comment->wasRecentlyCreated;

        return response()->json([
            'message' => $isCreate ? 'Comment created successfully' : 'Comment updated successfully',
            'data' => [
                'id' => $comment->id,
                'post_id' => $comment->post_id,
                'body' => $comment->body,
            ],
        ], $isCreate ? 201 : 200);
    }
}

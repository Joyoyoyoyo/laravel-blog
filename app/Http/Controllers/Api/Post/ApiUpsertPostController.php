<?php

namespace App\Http\Controllers\Api\Post;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\UpsertPostRequest;
use App\Models\Post;
use App\Models\User;
use App\Services\Post\UpsertPostService;
use Illuminate\Http\JsonResponse;

class ApiUpsertPostController extends Controller
{
    public function store(UpsertPostRequest $request, UpsertPostService $upsertPostService): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $post = $upsertPostService->create($user, $request->validated());

        return response()->json([
            'message' => 'Post created successfully',
            'data' => [
                'id' => $post->id,
                'title' => $post->title,
                'slug' => $post->slug,
            ],
        ], 201);
    }

    public function update(UpsertPostRequest $request, Post $post, UpsertPostService $upsertPostService): JsonResponse
    {
        $post = $upsertPostService->update($post, $request->validated());

        return response()->json([
            'message' => 'Post updated successfully',
            'data' => [
                'id' => $post->id,
                'title' => $post->title,
                'slug' => $post->slug,
            ],
        ]);
    }
}

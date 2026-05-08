<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserProfileResource;
use App\Models\User;
use App\Services\Post\PostFeedService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserProfilePageController extends Controller
{
    public function __invoke(Request $request, User $user, PostFeedService $feed): Response
    {
        $user->loadCount(['posts', 'likesReceived']);

        return Inertia::render('Users/Show', [
            'user' => UserProfileResource::make($user)->resolve($request),
            'authUser' => $request->user()?->only(['id', 'name']),
            'posts' => $feed->paginate($user->posts(), $request->user()),
        ]);
    }
}

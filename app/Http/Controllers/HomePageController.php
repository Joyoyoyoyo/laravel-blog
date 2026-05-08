<?php

namespace App\Http\Controllers;

use App\Services\Post\PostFeedService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HomePageController extends Controller
{
    public function __invoke(Request $request, PostFeedService $feed): Response
    {
        $user = $request->user();

        return Inertia::render('HomeBlank', [
            'user' => $user?->only(['id', 'name', 'email']),
            'posts' => $user !== null ? $feed->paginate($user->posts(), $user) : null,
        ]);
    }
}

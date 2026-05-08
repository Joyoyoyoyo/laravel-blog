<?php

namespace App\Http\Controllers\Bookmark;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Services\Post\PostFeedService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BookmarkIndexPageController extends Controller
{
    public function __invoke(Request $request, PostFeedService $feed): Response
    {
        $user = $request->user();

        $base = Post::query()->whereIn(
            'id',
            $user->bookmarks()->select('post_id'),
        );

        return Inertia::render('Bookmarks/Index', [
            'authUser' => $user->only(['id', 'name']),
            'posts' => $feed->paginate($base, $user),
        ]);
    }
}

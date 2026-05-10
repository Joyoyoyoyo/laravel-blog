<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\PostIndexRequest;
use App\Models\Post;
use App\Services\Post\PostFeedService;
use App\Support\CategoriesCache;
use Inertia\Inertia;
use Inertia\Response;

class PostIndexPageController extends Controller
{
    public function __invoke(PostIndexRequest $request, PostFeedService $feed): Response
    {
        $authUser = $request->user();
        $query = $request->applyFiltersTo(Post::query());

        return Inertia::render('Posts/Index', [
            'posts' => $feed->paginate($query, $authUser),
            'authUser' => $authUser?->only(['id', 'name']),
            'categories' => CategoriesCache::all(),
            'filters' => $request->filtersForFront(),
        ]);
    }
}

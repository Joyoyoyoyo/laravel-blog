<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PostEditorPageController extends Controller
{
    public function __invoke(?Post $post = null): Response
    {
        return Inertia::render('Posts/Editor', [
            'post' => $post?->only(['id', 'title', 'body', 'category_id']),
            'categories' => Category::query()
                ->orderBy('name')
                ->get(['id', 'name'])
                ->toArray(),
        ]);
    }
}

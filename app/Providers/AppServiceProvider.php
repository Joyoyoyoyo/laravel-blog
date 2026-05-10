<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Observers\CategoryObserver;
use App\Observers\DatabaseNotificationObserver;
use App\Policies\CommentPolicy;
use App\Policies\PostPolicy;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Post::class, PostPolicy::class);
        Gate::policy(Comment::class, CommentPolicy::class);

        Gate::define('manage-post', [PostPolicy::class, 'update']);
        Gate::define('manage-comment', [CommentPolicy::class, 'update']);

        Category::observe(CategoryObserver::class);
        DatabaseNotification::observe(DatabaseNotificationObserver::class);
    }
}

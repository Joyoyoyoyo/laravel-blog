<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Observers\CategoryObserver;
use App\Observers\DatabaseNotificationObserver;
use App\Policies\CommentPolicy;
use App\Policies\PostPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
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

        RateLimiter::for('login', fn (Request $request) => Limit::perMinute(10)->by($request->ip()));

        RateLimiter::for('web-api', function (Request $request) {
            $userId = $request->user()?->getAuthIdentifier();

            return Limit::perMinute(180)->by($userId !== null ? 'user:'.$userId : 'ip:'.$request->ip());
        });

        Category::observe(CategoryObserver::class);
        DatabaseNotification::observe(DatabaseNotificationObserver::class);
    }
}

<?php

use App\Http\Controllers\Api\Auth\ApiLoginController;
use App\Http\Controllers\Api\Auth\ApiLogoutController;
use App\Http\Controllers\Api\Comment\ApiUpsertCommentController;
use App\Http\Controllers\Api\Notification\ApiListNotificationsController;
use App\Http\Controllers\Api\Notification\ApiMarkNotificationReadController;
use App\Http\Controllers\Api\Post\ApiToggleBookmarkController;
use App\Http\Controllers\Api\Post\ApiTogglePostLikeController;
use App\Http\Controllers\Api\Post\ApiUpsertPostController;
use App\Http\Controllers\Api\Profile\ApiProfileController;
use App\Http\Controllers\Api\User\ApiToggleUserSubscriptionController;
use App\Http\Controllers\Bookmark\BookmarkIndexPageController;
use App\Http\Controllers\Auth\LoginPageController;
use App\Http\Controllers\Auth\RegisterPageController;
use App\Http\Controllers\HomePageController;
use App\Http\Controllers\Leaderboard\TopSubscribersLeaderboardController;
use App\Http\Controllers\Notification\NotificationIndexPageController;
use App\Http\Controllers\Post\PostEditorPageController;
use App\Http\Controllers\Post\PostIndexPageController;
use App\Http\Controllers\Profile\ProfileEditPageController;
use App\Http\Controllers\User\UserProfilePageController;
use App\Http\Controllers\User\UserSubscribersPageController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Home');
});

Route::get('/register', RegisterPageController::class);
Route::get('/login', LoginPageController::class)->name('login');

Route::post('/api/login', ApiLoginController::class)->middleware(['guest', 'throttle:login']);

Route::middleware('throttle:web-api')->group(function () {
    Route::post('/api/logout', ApiLogoutController::class)->middleware('auth')->name('logout');
    Route::post('/api/posts', [ApiUpsertPostController::class, 'store'])->middleware('auth');
    Route::put('/api/posts/{post}', [ApiUpsertPostController::class, 'update'])
        ->middleware(['auth', 'can:manage-post,post']);
    Route::post('/api/posts/{post}/like', ApiTogglePostLikeController::class)->middleware('auth');
    Route::post('/api/posts/{post}/bookmark', ApiToggleBookmarkController::class)->middleware('auth');
    Route::post('/api/users/{user}/subscribe', ApiToggleUserSubscriptionController::class)->middleware('auth');
    Route::post('/api/comments/save', ApiUpsertCommentController::class)->middleware('auth');

    Route::get('/api/notifications', ApiListNotificationsController::class)->middleware('auth');
    Route::post('/api/notifications/{notification}/read', [ApiMarkNotificationReadController::class, 'single'])
        ->middleware('auth');
    Route::post('/api/notifications/read-all', [ApiMarkNotificationReadController::class, 'all'])
        ->middleware('auth');
});

Route::middleware(['auth', 'throttle:web-api'])->group(function () {
    Route::get('/notifications', NotificationIndexPageController::class)->name('notifications.index');

    Route::get('/home', HomePageController::class)->name('home');

    Route::get('/posts/editor', PostEditorPageController::class)->name('posts.editor.create');

    Route::get('/posts/editor/{post}', PostEditorPageController::class)
        ->middleware('can:manage-post,post')
        ->name('posts.editor.edit');

    Route::get('/bookmarks', BookmarkIndexPageController::class)->name('bookmarks.index');

    Route::get('/profile', ProfileEditPageController::class)->name('profile.edit');
    Route::put('/api/profile', [ApiProfileController::class, 'update'])->name('profile.update');
    Route::put('/api/profile/password', [ApiProfileController::class, 'updatePassword'])
        ->name('profile.password.update');
    Route::delete('/api/profile', [ApiProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/posts', PostIndexPageController::class)->name('posts.index');

Route::get('/leaderboard/subscribers', TopSubscribersLeaderboardController::class)->name('leaderboard.subscribers');

Route::get('/users/{user}/subscribers', UserSubscribersPageController::class)->name('users.subscribers');
Route::get('/users/{user}', UserProfilePageController::class)->name('users.show');

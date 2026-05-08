<?php

use App\Http\Controllers\Api\Auth\ApiLoginController;
use App\Http\Controllers\Api\Comment\ApiUpsertCommentController;
use App\Http\Controllers\Api\Notification\ApiListNotificationsController;
use App\Http\Controllers\Api\Notification\ApiMarkNotificationReadController;
use App\Http\Controllers\Api\Post\ApiTogglePostLikeController;
use App\Http\Controllers\Api\Post\ApiUpsertPostController;
use App\Http\Controllers\Auth\LoginPageController;
use App\Http\Controllers\Auth\RegisterPageController;
use App\Http\Controllers\HomePageController;
use App\Http\Controllers\Notification\NotificationIndexPageController;
use App\Http\Controllers\Post\PostEditorPageController;
use App\Http\Controllers\Post\PostIndexPageController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Home');
});

Route::get('/register', RegisterPageController::class);
Route::get('/login', LoginPageController::class)->name('login');

Route::post('/api/login', ApiLoginController::class)->middleware('guest');
Route::post('/api/posts', [ApiUpsertPostController::class, 'store'])->middleware('auth');
Route::put('/api/posts/{post}', [ApiUpsertPostController::class, 'update'])
    ->middleware(['auth', 'can:manage-post,post']);
Route::post('/api/posts/{post}/like', ApiTogglePostLikeController::class)->middleware('auth');
Route::post('/api/comments/save', ApiUpsertCommentController::class)->middleware('auth');

Route::get('/api/notifications', ApiListNotificationsController::class)->middleware('auth');
Route::post('/api/notifications/{notification}/read', [ApiMarkNotificationReadController::class, 'single'])
    ->middleware('auth');
Route::post('/api/notifications/read-all', [ApiMarkNotificationReadController::class, 'all'])
    ->middleware('auth');

Route::get('/notifications', NotificationIndexPageController::class)
    ->middleware('auth')
    ->name('notifications.index');

Route::get('/home', HomePageController::class)->middleware('auth')->name('home');

Route::get('/posts/editor', PostEditorPageController::class)
    ->middleware('auth')
    ->name('posts.editor.create');

Route::get('/posts/editor/{post}', PostEditorPageController::class)
    ->middleware(['auth', 'can:manage-post,post'])
    ->name('posts.editor.edit');

Route::get('/posts', PostIndexPageController::class)->name('posts.index');

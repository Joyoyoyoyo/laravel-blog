<?php

use App\Http\Controllers\Api\Auth\ApiRegisterController;
use Illuminate\Support\Facades\Route;

Route::post('/register', ApiRegisterController::class)->middleware('throttle:login');

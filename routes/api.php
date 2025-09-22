<?php

use Illuminate\Support\Facades\Route;
use App\http\Controllers\NewsController;
use App\http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');
Route::post('/refresh', [AuthController::class, 'refresh']);

Route::middleware(['jwt', 'throttle:api'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'getUser']);
    Route::apiResource('users', UserController::class);
    Route::apiResource('news', NewsController::class);
    Route::apiResource('categories', CategoryController::class);
});
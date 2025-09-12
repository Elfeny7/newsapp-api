<?php

use Illuminate\Support\Facades\Route;
use App\http\Controllers\NewsController;
use App\http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('jwt')->group(function () {
    Route::apiResource('users', AuthController::class);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('news', NewsController::class);
    Route::apiResource('categories', CategoryController::class);
});
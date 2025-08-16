<?php

use Illuminate\Support\Facades\Route;
use App\http\Controllers\NewsController;
use App\http\Controllers\AuthController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('jwt')->group(function () {
    Route::apiResource('news', NewsController::class);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/get-user', [AuthController::class, 'getUser']);
});
<?php

use Illuminate\Support\Facades\Route;
use App\http\Controllers\NewsController;
use App\http\Controllers\AuthController;

Route::apiResource('news', NewsController::class);
Route::post('/register', [AuthController::class, 'register']);
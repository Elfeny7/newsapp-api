<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\http\Controllers\NewsController;

Route::apiResource('news', NewsController::class);

<?php

use App\Http\Middleware\JwtMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Support\ApiResponse;
use App\Exceptions\InvalidCredentialsException;
use App\Exceptions\UserNotFoundException;
use Tymon\JWTAuth\Exceptions\JWTException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'jwt' => JwtMiddleware::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e, $request) {
            if ($request->is('api/*')) {
                if ($e instanceof \Illuminate\Validation\ValidationException) {
                    return ApiResponse::validationError($e->errors(), 'Validation failed', 422);
                }
                if ($e instanceof \Tymon\JWTAuth\Exceptions\JWTException) {
                    return ApiResponse::throw($e, $e->getMessage() ?: 'Authentication failed', 401);
                }
                if ($e instanceof \App\Exceptions\InvalidCredentialsException) {
                    return ApiResponse::throw($e, 'Invalid Credentials', 401);
                }
                if ($e instanceof \App\Exceptions\UserNotFoundException) {
                    return ApiResponse::throw($e, 'User not found', 404);
                }
                if ($e instanceof \Illuminate\Auth\AuthenticationException) {
                    return ApiResponse::throw($e, 'Unauthorized', 401);
                }
                if ($e instanceof \Illuminate\Auth\Access\AuthorizationException) {
                    return ApiResponse::throw($e, 'Forbidden', 403);
                }
                if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                    return ApiResponse::throw($e, 'Resource not found', 404);
                }
                if ($e instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException) {
                    return ApiResponse::throw($e, 'Method not allowed', 405);
                }
                if ($e instanceof \Illuminate\Http\Exceptions\ThrottleRequestsException) {
                    return ApiResponse::throw($e, 'Too many requests', 429);
                }
                return ApiResponse::throw($e, 'Internal server error', 500);
            }
        });
    })->create();

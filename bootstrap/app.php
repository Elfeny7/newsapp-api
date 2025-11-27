<?php

use App\Http\Middleware\JwtMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Helper\ApiResponse;

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
                if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                    return ApiResponse::error($e, 'Token expired', 401);
                }
                if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                    return ApiResponse::error($e, 'Token invalid', 401);
                }
                if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenBlacklistedException) {
                    return ApiResponse::error($e, 'Token blacklisted', 401);
                }
                if ($e instanceof \Tymon\JWTAuth\Exceptions\JWTException) {
                    return ApiResponse::error($e, 'Token not provided', 400);
                }
                if ($e instanceof \App\Exceptions\InvalidCredentialsException) {
                    return ApiResponse::error($e, 'Invalid Credentials', 401);
                }
                if ($e instanceof \App\Exceptions\UserNotFoundException) {
                    return ApiResponse::error($e, 'User not found', 404);
                }
                if ($e instanceof \Illuminate\Auth\AuthenticationException) {
                    return ApiResponse::error($e, 'Unauthorized', 401);
                }
                if ($e instanceof \Illuminate\Auth\Access\AuthorizationException) {
                    return ApiResponse::error($e, 'Forbidden', 403);
                }
                if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                    return ApiResponse::error($e, 'Resource not found', 404);
                }
                if ($e instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException) {
                    return ApiResponse::error($e, 'Method not allowed', 405);
                }
                if ($e instanceof \Illuminate\Http\Exceptions\ThrottleRequestsException) {
                    return ApiResponse::error($e, 'Too many requests', 429);
                }
                return ApiResponse::error($e, 'Internal server error', 500);
            }
        });
    })->create();

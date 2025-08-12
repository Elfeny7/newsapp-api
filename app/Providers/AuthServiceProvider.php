<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\AuthServiceInterface;
use App\Interfaces\TokenServiceInterface;
use App\Services\AuthService;
use App\Services\JWTTokenService;

class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AuthServiceInterface::class, AuthService::class);
        $this->app->bind(TokenServiceInterface::class, JWTTokenService::class);
    }

    public function boot(): void
    {
        //
    }
}

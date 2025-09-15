<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Interfaces\AuthServiceInterface;
use App\Interfaces\TokenServiceInterface;
use App\Services\AuthService;
use App\Services\TokenService;
use App\Policies\UserPolicy;
use App\Policies\NewsPolicy;
use App\Policies\CategoryPolicy;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        \App\Models\News::class => \App\Policies\NewsPolicy::class
    ];

    public function register(): void
    {
        $this->app->bind(AuthServiceInterface::class, AuthService::class);
        $this->app->bind(TokenServiceInterface::class, TokenService::class);
    }

    public function boot(): void
    {
        $this->registerPolicies();
        Gate::policy('manage-user', UserPolicy::class);
        Gate::policy('manage-news', NewsPolicy::class);
        Gate::policy('manage-category', CategoryPolicy::class);
    }
}

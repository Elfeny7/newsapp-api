<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\NewsServiceInterface;
use App\Interfaces\CategoryServiceInterface;
use App\Services\CategoryService;
use App\Services\NewsService;
use App\Interfaces\UserServiceInterface;
use App\Services\UserService;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(NewsServiceInterface::class, NewsService::class);
        $this->app->bind(CategoryServiceInterface::class, CategoryService::class);
        $this->app->bind(UserServiceInterface::class, UserService::class);
    }
    
    public function boot(): void
    {

    }
}

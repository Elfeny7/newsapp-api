<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\NewsServiceInterface;
use App\Interfaces\CategoryServiceInterface;
use App\Services\CategoryService;
use App\Services\NewsService;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(NewsServiceInterface::class, NewsService::class);
        $this->app->bind(CategoryServiceInterface::class, CategoryService::class);
    }
    
    public function boot(): void
    {

    }
}

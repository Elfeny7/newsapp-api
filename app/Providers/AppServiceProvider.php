<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\NewsServiceInterface;
use App\Services\NewsService;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(NewsServiceInterface::class, NewsService::class);
    }
    
    public function boot(): void
    {

    }
}

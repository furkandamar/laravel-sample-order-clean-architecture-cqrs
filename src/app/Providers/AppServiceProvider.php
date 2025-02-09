<?php

namespace App\Providers;

use App\Infrastructure\Abstraction\Service\IAuthService;
use App\Infrastructure\Abstraction\Service\IOrderService;
use App\Infrastructure\Abstraction\Service\IProductService;
use App\Infrastructure\Services\AuthService;
use App\Infrastructure\Services\OrderService;
use App\Infrastructure\Services\ProductService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(IAuthService::class, AuthService::class);
        $this->app->bind(IProductService::class, ProductService::class);
        $this->app->bind(IOrderService::class, OrderService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

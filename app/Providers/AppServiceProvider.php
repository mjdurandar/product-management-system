<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\ProductInterface;
use App\Services\FakeStoreApiService;
use App\Services\PlatziApiService;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ProductInterface::class, function ($app) {
            // Switch between FakeStoreApiService and PlatziApiService as needed
            // return new FakeStoreApiService();
            return new PlatziApiService();
        });
    }

    public function boot(): void
    {
        //
    }
}

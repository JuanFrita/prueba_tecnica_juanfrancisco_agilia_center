<?php

namespace App\Providers;

use App\Repositories\Movies\Eloquent\MovieEloquentRepository;
use App\Repositories\Movies\MovieRepositoryInterface;
use Illuminate\Contracts\Foundation\Application;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(MovieRepositoryInterface::class, function (Application $app) {
            return new MovieEloquentRepository();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

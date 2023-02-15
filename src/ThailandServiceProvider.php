<?php

namespace Nttps\Thailand;

use Illuminate\Support\ServiceProvider;

class ThailandServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/migrations');
        $this->publishes([
            __DIR__ . '/views'  => resource_path('/migrations')
        ], 'thailand');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}

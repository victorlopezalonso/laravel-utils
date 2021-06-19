<?php

namespace Victorlopezalonso\LaravelUtils;

use Illuminate\Routing\Router;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider;
use Victorlopezalonso\LaravelUtils\Classes\Config;
use Victorlopezalonso\LaravelUtils\Http\Middleware\CheckHeadersMiddleware;
use Victorlopezalonso\LaravelUtils\Http\Middleware\LocalizationMiddleware;

class LaravelUtilsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'laravel-utils');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-utils');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('checkHeaders', CheckHeadersMiddleware::class);

        $kernel = $this->app->make(Kernel::class);
        $kernel->pushMiddleware(LocalizationMiddleware::class);

        Config::init();

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('laravel-utils.php'),
            ], 'config');

            // $this->publishes([
            //     __DIR__.'/../helpers' => config_path('laravel-utils.php'),
            // ], 'helpers');

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/laravel-utils'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/laravel-utils'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/laravel-utils'),
            ], 'lang');*/

            // Registering package commands.
            // $this->commands([]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'laravel-utils');


        // Register the main class to use with the facade
        $this->app->singleton('laravel-utils', function () {
            return new LaravelUtils;
        });
    }
}

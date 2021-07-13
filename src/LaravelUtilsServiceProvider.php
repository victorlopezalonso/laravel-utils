<?php

namespace Victorlopezalonso\LaravelUtils;

use Illuminate\Routing\Router;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider;
use Victorlopezalonso\LaravelUtils\Classes\Config;
use Victorlopezalonso\LaravelUtils\Console\Commands\LaravelInfo;
use Victorlopezalonso\LaravelUtils\Console\Commands\LaravelInit;
use Victorlopezalonso\LaravelUtils\Providers\EventServiceProvider;
use Victorlopezalonso\LaravelUtils\Console\Commands\LaravelConfigEmail;
use Victorlopezalonso\LaravelUtils\Http\Middleware\CheckHeadersMiddleware;
use Victorlopezalonso\LaravelUtils\Http\Middleware\LocalizationMiddleware;
use Victorlopezalonso\LaravelUtils\Console\Commands\LaravelCreateAdminUser;
use Victorlopezalonso\LaravelUtils\Console\Commands\LaravelCreateSupervisorFiles;
use Victorlopezalonso\LaravelUtils\Console\Commands\LaravelConfigPushNotifications;

class LaravelUtilsServiceProvider extends ServiceProvider
{
    private function initMiddlewares()
    {
        $router = $this->app->make(Router::class);
        $router->pushMiddlewareToGroup('api', CheckHeadersMiddleware::class);

        $kernel = $this->app->make(Kernel::class);
        $kernel->pushMiddleware(LocalizationMiddleware::class);
    }

    private function publishCommands()
    {
        $this->commands([
            LaravelConfigEmail::class,
            LaravelConfigPushNotifications::class,
            LaravelCreateAdminUser::class,
            LaravelCreateSupervisorFiles::class,
            LaravelInfo::class,
            LaravelInit::class,
        ]);
    }

    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        Config::init();

        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'laravel-utils');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-utils');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->loadRoutesFrom(__DIR__.'/routes/web.php');

        $this->initMiddlewares();

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('laravel-utils.php'),
            ], 'config');

            $this->publishCommands();

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
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'laravel-utils');

        // $this->app->register(EventServiceProvider::class);
        // $this->app->register(MySocialServiceProvider::class);

        // Register the main class to use with the facade
        $this->app->singleton('laravel-utils', function () {
            return new LaravelUtils;
        });
    }
}

<?php

namespace Marcoaacoliveira\LaravelPagseguro;

use Illuminate\Support\ServiceProvider;

class LaravelPagseguroServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'marcoaacoliveira');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'marcoaacoliveira');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }

        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/laravelpagseguro.php', 'laravelpagseguro');

        // Register the service the package provides.
        $this->app->singleton('laravelpagseguro', function ($app) {
            return new LaravelPagseguro;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['laravelpagseguro'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/laravelpagseguro.php' => config_path('laravelpagseguro.php'),
        ], 'laravelpagseguro.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/marcoaacoliveira'),
        ], 'laravelpagseguro.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/marcoaacoliveira'),
        ], 'laravelpagseguro.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/marcoaacoliveira'),
        ], 'laravelpagseguro.views');*/

        // Registering package commands.
        // $this->commands([]);
    }
}

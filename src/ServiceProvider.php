<?php

namespace NickDeKruijk\Admin;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/views', 'admin');
        $this->publishes([
            __DIR__.'/config.php' => config_path('admin.php'),
        ], 'config');
        $this->loadRoutesFrom(__DIR__.'/routes.php');
        if (config('admin.role_column_migration')) {
            $this->loadMigrationsFrom(__DIR__.'/migrations/role_column');
        }
        $this->loadTranslationsFrom(__DIR__.'/lang', 'admin');
        if ($this->app->runningInConsole()) {
            $this->commands([
                UserCommand::class,
            ]);
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/config.php', 'admin');
    }
}

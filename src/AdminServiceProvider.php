<?php

namespace NickDeKruijk\Admin;

use \Illuminate\Support\ServiceProvider;
use Livewire;
use NickDeKruijk\Admin\Commands\UserCommand;

class AdminServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Enable publishing of config file.
        $this->publishes([
            __DIR__ . '/config.php' => config_path('admin.php'),
        ], 'config');

        // Load the routes needed for admin.
        $this->loadRoutesFrom(__DIR__ . '/routes.php');

        // Hint path for admin views.
        $this->loadViewsFrom(__DIR__ . '/views', 'admin');

        // Load the translations JSON files.
        $this->loadJSONTranslationsFrom(__DIR__ . '/lang');

        // Register all Livewire admin components.
        foreach (glob(__DIR__ . '/Livewire/*.php') as $file) {
            Livewire::component('admin.' . strtolower(basename($file, '.php')), 'NickDeKruijk\Admin\Livewire\\' . basename($file, '.php'));
        }

        // Add artisan commands.
        if ($this->app->runningInConsole()) {
            $this->commands([
                UserCommand::class,
            ]);
        }

        // Include the required migrations.
        $this->loadMigrationsFrom(__DIR__ . '/migrations');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // Get default config values
        $this->mergeConfigFrom(__DIR__ . '/config.php', 'admin');
    }
}

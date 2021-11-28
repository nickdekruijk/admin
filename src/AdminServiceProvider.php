<?php

namespace NickDeKruijk\Admin;

use \Illuminate\Support\ServiceProvider;
use Livewire;
use NickDeKruijk\Admin\Commands\UserCommand;
use NickDeKruijk\Admin\Livewire\Login;

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
        Livewire::component('admin.login', Login::class);

        // Add artisan commands.
        if ($this->app->runningInConsole()) {
            $this->commands([
                UserCommand::class,
            ]);
        }

        // if (config('admin.role_column_migration')) {
        //     $this->loadMigrationsFrom(__DIR__ . '/migrations/role_column');
        // }
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

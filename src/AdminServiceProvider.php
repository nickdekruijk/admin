<?php

namespace NickDeKruijk\Admin;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Livewire;
use NickDeKruijk\Admin\Commands\UserCommand;
use NickDeKruijk\Admin\Models\Permission;

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

        // Register authentication gates modules can use to check user permissions.
        Gate::define('admin.any', function ($user, $module) {
            return Permission::currentUser()->canAny($module::class);
        });
        Gate::define('admin.create', function ($user, $module) {
            return Permission::currentUser()->canCreate($module::class);
        });
        Gate::define('admin.read', function ($user, $module) {
            return Permission::currentUser()->canRead($module::class);
        });
        Gate::define('admin.update', function ($user, $module) {
            return Permission::currentUser()->canUpdate($module::class);
        });
        Gate::define('admin.delete', function ($user, $module) {
            return Permission::currentUser()->canDelete($module::class);
        });

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

[![Latest Stable Version](https://poser.pugx.org/nickdekruijk/admin/v/stable)](https://packagist.org/packages/nickdekruijk/admin)
[![Latest Unstable Version](https://poser.pugx.org/nickdekruijk/admin/v/unstable)](https://packagist.org/packages/nickdekruijk/admin)
[![Monthly Downloads](https://poser.pugx.org/nickdekruijk/admin/d/monthly)](https://packagist.org/packages/nickdekruijk/admin)
[![Total Downloads](https://poser.pugx.org/nickdekruijk/admin/downloads)](https://packagist.org/packages/nickdekruijk/admin)
[![License](https://poser.pugx.org/nickdekruijk/admin/license)](https://packagist.org/packages/nickdekruijk/admin)

# Admin
An easy to implement, lightweight yet complete Laravel 8+ admin panel/backend and media/filemanager build with Laravel Livewire.

## Installation
To install the package use

`composer require nickdekruijk/admin`

## Configuration
After installing for the first time publish the config file with 

`php artisan vendor:publish --tag=config --provider="NickDeKruijk\Admin\AdminServiceProvider"` 

A default config file called `admin.php` will be available in your Laravel `app/config` folder. See this file for more details. Some important configuration options are highlighted below.

### /admin
By default you access admin panel by adding /admin to the URL of your website/application. For example https://www.domain.com/admin
You can change this path by changing the `route_prefix` configuration option.

### Give users permissions
Admin uses a permissions table to check which users have access to which modules. A migration for this table is already included so just run `php artisan migrate`. If required change the `table_prefix` in your configuration file first.

### Configure modules
Change the `modules` array in our configuration file to include the modules you want to use. Each module is a class that should use the `NickDeKruijk\Admin\Traits\AdminModule` trait. This can be a Laravel Model or Livewire component for example.

Each module has an admin_config array that can be used to configure the module. See [`Classes/AdminConfig.php`](https://github.com/nickdekruijk/admin/blob/2.0/src/Classes/AdminConfig.php) for available options.

### Creating a new user
If your application has no users Admin provides an artisan console command to create or update a user:

`php artisan admin:user <email> [<name>]`

If the name of the user contains spaces you should use quotes around it, e.g. `php artisan admin:user git@nickdekruijk.nl "Nick de Kruijk"`. When using this command you will be prompted for a password and if you want to give the user all availabe permissions.

## FAQ

### How do I localize the validation messages?
You could use the package [arcanedev/laravel-lang](https://github.com/ARCANEDEV/LaravelLang), just run `composer require arcanedev/laravel-lang`.

## License
Admin is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

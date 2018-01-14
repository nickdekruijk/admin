# LaraPages
A simple, lightweight yet complete Laravel 5.5+ admin panel/backend and media/filemanager.
Basically it's a web-based content editor for your Laravel models. Edit the config/larapages.php file to specify the models you want to manage.
It's very easy to integrate it in your current Laravel application.

## Installation
To install the package use  
`composer require larapages/admin`  
  
After installing for the first time publish the config with
`php artisan vendor:publish --tag=config --provider="LaraPages\Admin\ServiceProvider"`

## Requirements
LaraPages uses the auth middleware from Laravel. To determine if a user has permission you need to add a `admin_role` column to your User model. The value must match one of the roles defined in the configuration. If your application already uses authentication this is probably the only change you need to make.

If your app doesn't use authentication yet you might need to run `php artisan make:auth` and optionally customise the views/routes or, if you only want a simple login screen without registration, 'Remember me' or password resets, you can add `LaraPages\Admin\LoginController::routes();` to your routes/web.php file.

## Configuration
After installation (if you did `php artisan vendor:publish`) a default config file called `larapages.php` will be available in your Laravel `app/config` folder. See this file for more details.

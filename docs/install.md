# Installation

## Using composer
To install the package use

`composer require nickdekruijk/admin`

## Initial configuration
After installing for the first time publish the config file with 

`php artisan vendor:publish --tag=config --provider="NickDeKruijk\Admin\ServiceProvider"` 

A default config file called `admin.php` will be available in your Laravel `app/config` folder. See this file for more details. See the [configuration](config.md) documentation for more information.

## Add 'admin_role' to your users
Admin uses the auth middleware from Laravel. To determine if a user has permission you need to add a `admin_role` column to your User model and table. You can change the column name with the `role_column` configuration option.
A migration is included with the package so you can run `php artisan migrate` to add the column. If you don't want to use the included migration you can disable it by changing the configuration option `role_column_migration` to false. 

## Configure modules and roles
The most important configuration option you probably need to change is the `modules` array. See the [configuration](config.md) documentation for more information.

## Login routes
By default Admin will register login and logout routes and use a simple login screen without registration, 'Remember me' or password resets.
If your application already uses authentication your routes/web.php file will probably overwrite these routes but you probably want to disable the Admin routes by changing configuration option `auth_routes` to false.

## Creating a new user
If your application has no users Admin provides an artisan console command to create or update a user with a random password and assign a role.

`php artisan admin:user <email> [<role>]`

Role must match one of the roles defined in the configuration. The default role for a new user is "admin".
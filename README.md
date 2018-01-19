# In early development, missing core functionality
For now see [nickdekruijk/larapages](https://github.com/nickdekruijk/larapages) to get an idea where this is heading.

# LaraPages
A simple, lightweight yet complete Laravel 5.5+ admin panel/backend and media/filemanager.
Basically it's a web-based content editor for your Laravel models. Edit the config/larapages.php file to specify the models you want to manage.
It's very easy to integrate it in your current Laravel application.

larapages/admin is the next evolution of [nickdekruijk/larapages](https://github.com/nickdekruijk/larapages) which won't be updated anymore but will remain online for historical reference.

## Installation
To install the package use  
`composer require larapages/admin`  
  
After installing for the first time publish the config with
`php artisan vendor:publish --tag=config --provider="LaraPages\Admin\ServiceProvider"`

## Requirements
LaraPages uses the auth middleware from Laravel. To determine if a user has permission you need to add a `admin_role` column to your User model. The value must match one of the roles defined in the [configuration](#configuration). If your application already uses authentication this is probably the only change you need to make.


## Configuration
After installation (if you did `php artisan vendor:publish`) a default config file called `larapages.php` will be available in your Laravel `app/config` folder. See this file for more details.
### Login routes
By default LaraPages will register login and logout routes and use a simple login screen without registration, 'Remember me' or password resets.
If your application already uses authentication your routes/web.php file will probably overwrite these routes but you probably want to disable the LaraPages routes by setting auth_routes to false in the config file.

## Creating a new user
When starting a new project or your project didn't use authentication you probably don't have any users yet. LaraPages provides an artisan console command to create or update a user with a random password and assign a role.
`php artisan larapages:user <email> [<role>]`
Role must match one of the roles defined in the [configuration](#configuration). The default role for a new user is "admin". 

## License
LaraPages is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
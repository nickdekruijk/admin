# Configuration
After installing for the first time publish the config file with 

`php artisan vendor:publish --tag=config --provider="NickDeKruijk\Admin\ServiceProvider"` 

A default config file called `admin.php` will be available in your Laravel `app/config` folder. See this file for more details. Most options are well documentented in there but some require some deeper explanation, see below.

## Configuring modules

### Modules introduction
Each option in the admin panel main navigation menu is called a module. Let's use this simple modules example:
```php
    'modules' => [
        'dashboard' => [
            'view' => 'admin::dashboard',
            'icon' => 'fa-dashboard',
        ],
        'posts' => [
            'view' => 'admin::model',
            'icon' => 'fa-sitemap',
            'title' => 'Blog posts',
            'title_nl' => 'Blog artikelen',
            'model' => 'App\Post',
        ],
        'users' => [
            'view' => 'admin::model',
            'icon' => 'fa-users',
            'model' => 'App\User',
            'index' => 'email,name,admin_role',
```
Each module must be defined on the first level of the array. The array key is used as the url slug, so it will be accesses by /admin/dashboard or /admin/posts. Each module must at least have a view and a [fontawesome.io](https://fontawesome.com/v4.7.0/icons/) icon.

### Modules reference
See the [modules reference](modules-reference.md) for more details.
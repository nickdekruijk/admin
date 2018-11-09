# Installation

## Using composer
To install the package use

`composer require nickdekruijk/admin`

## Initial configuration
After installing for the first time publish the config file with 

`php artisan vendor:publish --tag=config --provider="NickDeKruijk\Admin\ServiceProvider"` 

A default config file called `admin.php` will be available in your Laravel `app/config` folder. See this file for more details. See the [configuration](config.md) documentation for more information.

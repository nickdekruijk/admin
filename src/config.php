<?php

use Illuminate\Support\Facades\Auth;

return [

    /*
    |--------------------------------------------------------------------------
    | route_prefix
    |--------------------------------------------------------------------------
    | All admin routes will be prefixed with this path and is also used as the
    | uri to login, e.g. 'admin' for www.domain.com/admin
    */
    'route_prefix' => 'admin',

    /*
    |--------------------------------------------------------------------------
    | table_prefix
    |--------------------------------------------------------------------------
    | The package inclused migrations to create tables. The created tables name
    | will use this prefix, e.g. 'admin_' for admin_permissions.
    */
    'table_prefix' => 'admin_',

    /*
    |--------------------------------------------------------------------------
    | logo
    |--------------------------------------------------------------------------
    | This html code is shown in the upper left corner
    */
    'logo' => '<a href="/" target="_blank"><i class="fa-brands fa-laravel" style="font-size:32px;float:left;margin-right:5px"></i>Admin</a>',

    /*
    |--------------------------------------------------------------------------
    | guard
    |--------------------------------------------------------------------------
    | The guard to use when trying to login a user, e.g. 'web' that uses the
    | default User model. To seperate application users from admin users define
    | a new guard in the config/auth.php file.
    */
    'guard' => Auth::getDefaultDriver(),

    /*
    |--------------------------------------------------------------------------
    | credentials
    |--------------------------------------------------------------------------
    | The credentials to use when logging in a user, e.g. ['email', 'password']
    */
    'credentials' => ['email', 'password'],

    /*
    |--------------------------------------------------------------------------
    | components
    |--------------------------------------------------------------------------
    | All components available to admin users. The navigation structure will 
    | also be generated from this array. The first component will be the 
    | default a user sees after login.
    */
    'components' => [
        'Dashboard' => 'admin-dashboard',
        'Content' => [
            'Pages' => 'admin-crud:App\Models\Page',
        ],
        'User Management' => [
            'Users' => 'admin-crud:App\Models\User',
            'Permissions' => 'admin-crud:NickDeKruijk\Admin\Models\Permissions',
        ],
    ],

];

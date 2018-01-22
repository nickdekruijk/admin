<?php

return [

    /*
    |--------------------------------------------------------------------------
    | adminpath
    |--------------------------------------------------------------------------
    | The url used to login. e.g. 'lp-admin' for www.domain.com/lp-admin
    */
    'adminpath' => 'lp-admin',

    /*
    |--------------------------------------------------------------------------
    | logo
    |--------------------------------------------------------------------------
    | This html code is shown in the upper left corner
    */
    'logo' => '<i class="fa fa-first-order logo"></i>LaraPages',

    /*
    |--------------------------------------------------------------------------
    | save_on_enter
    |--------------------------------------------------------------------------
    | Hitting enter key while inserting data in forms will submit/save
    */
    'save_on_enter' => true,

    /*
    |--------------------------------------------------------------------------
    | auth_routes
    |--------------------------------------------------------------------------
    | Register authentication routes for login and logout. Disable these if you
    | want to use Laravels Auth::routes() or customize it yourself
    */
    'auth_routes' => true,

    /*
    |--------------------------------------------------------------------------
    | modules
    |--------------------------------------------------------------------------
    | All editable models, dashboard, reports, etc. should be defined here.
    | The main navigation will be build with this too.
    | 'icon' should be a valid http://fontawesome.io/icons/ icon.
    */
    'modules' => [
        'dashboard' => [
            'view' => 'larapages::dashboard',
            'icon' => 'fa-dashboard',
        ],
        'pages' => [
            'view' => 'larapages::model',
            'icon' => 'fa-sitemap',
            'title' => 'Website pages', # If ommited defaults to ucfirst(id)
            'title_nl' => 'Website pagina\'s',
            'button_new' => 'Create new page', # If ommited defaults to 'New'
            'treeview' => 'parent',
            'index' => 'title,id,head,slug,html_title',
            'model' => 'App\Page',
            'orderBy' => 'sort,id',
            'columns' => [
                'active' => [
                    'default' => true,
                ],
                'hidden',
                'home',
                'title' => [
                    'validate' => 'required',
                ],
                'view',
                'head',
                'html_title',
                'slug' => [
                    'placeholder' => 'the url slug, is added to the page url',
                    'placeholder_nl' => 'Unieke \'slug\'',
                ],
                'description',
                'date' => [
                    'validate' => 'nullable|date',
                ],
                'pictures',
                'background',
                'body',
            ],
            'sortable' => true,
        ],
        'media' => [
            'view' => 'larapages::media',
            'icon' => 'fa-picture-o',
            'maxUploadSize' => '12', # Maximum size of an uploaded file in megabytes, still limited by php.ini upload_max_filesize and post_max_size
            'folder' => 'media',     # Base folder to store uploaded files. Will be public_path(this)
        ],
        'reports' => [
            'view' => 'larapages::reports',
            'icon' => 'fa-print',
            'title_nl' => 'Rapporten',
            'disabled' => false,
            'queries' => [
                'All pages' => 'SELECT * FROM pages',
            ],
        ],
        'users' => [
            'view' => 'larapages::model',
            'icon' => 'fa-users',
            'title_nl' => 'Gebruikers',
            'model' => 'App\User',
            'columns' => [
                'name' => [
                    'validate' => 'required',
                ],
                'email' => [
                    'validate' => 'required|email|unique:users,email,#id#|required',
                ],
                'password' => [
                    'type' => 'password',
                    'validate' => 'required|min:8',
                ],
            ],
            'index' => 'email,name',
            'orderBy' => 'created_at desc',
        ],
        'settings' => [
            'view' => 'larapages::model',
            'icon' => 'fa-cog',
            'title_nl' => 'Instellingen',
            'model' => 'LaraPages\Settings\Setting',
            'index' => 'key,value,description',
            'new' => 'Add new setting',
            'new_nl' => 'Instelling toevoegen',
            'columns' => [
                'key' => [
                    'title' => 'Setting',
                    'title_nl' => 'Instelling',
                    'validate' => 'unique:settings,key,#id#|required',
                ],
                'description' => [
                    'title_nl' => 'Omschrijving',
                ],
                'value' => [
                    'title_nl' => 'Waarde',
                    'validate' => 'required',
                ],
            ],
            'orderBy' => 'key',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | roles
    |--------------------------------------------------------------------------
    | The roles the users can be assigned to.
    | Must match 'modules' items (see above).
    | Make sure the User model has a column matching the 'role_column' value
    | The 'role_column' of a user determines if a user has access to LaraPages
    | The 'role_column_migration' enables or disables the included migration
    */
    'role_column' => 'admin_role',
    'role_column_migration' => true,
    'roles' => [
        'admin' => [
            'title' => 'Administrator',
        ],
        'cms' => [
            'title' => 'Content manager',
            'permissions' => [
                'dashboard' => [ 'read' ],
                'pages' => [ 'create', 'read', 'update' ],
                'media' => [ 'create', 'read', 'update' ],
                'settings' => [ 'read', 'update' ],
                'reports' => [ 'read' ],
            ],
        ],
    ],

];

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
    | modules
    |--------------------------------------------------------------------------
    | All editable models, dashboard, reports, etc. should be defined here.
    | The main navigation will be build with this too.
    | 'icon' should be a valid http://fontawesome.io/icons/ icon.
    */
    'modules' => [
        'dashboard' => [
            'icon' => 'fa-dashboard',
            'view' => 'larapages::dashboard',
        ],
        'pages' => [
            'title' => 'Website pages', # If ommited defaults to ucfirst(id)
            'title_nl' => 'Website pagina\'s',
            'button_new' => 'Create new page', # If ommited defaults to 'New'
            'icon' => 'fa-sitemap',
            'view' => 'larapages::model',
            'treeview' => 'parent',
            'index' => 'title,id,head,subhead,slug,seo_title',
            'model' => 'App\Page',
            'orderBy' => 'sort',
            'sortable' => true,
        ],
        'media' => [
            'icon' => 'fa-picture-o',
            'view' => 'larapages::media',
            'maxUploadSize' => '12', # Maximum size of an uploaded file in megabytes, still limited by php.ini upload_max_filesize and post_max_size
            'folder' => 'media',     # Base folder to store uploaded files. Will be public_path(this)
        ],
        'reports' => [
            'icon' => 'fa-print',
            'title_nl' => 'Rapporten',
            'disabled' => false,
            'view' => 'larapages::reports',
            'queries' => [
                'All pages' => 'SELECT * FROM pages',
            ],
        ],
        'users' => [
            'icon' => 'fa-users',
            'title_nl' => 'Gebruikers',
            'view' => 'larapages::model',
            'model' => 'App\User',
            'columns' => [
                'name',
                'email',
                'password' => [
                    'type' => 'password',
                ]
            ],
            'index' => 'email,name',
            'orderBy' => 'created_at desc',
        ],
        'settings' => [
            'title_nl' => 'Instellingen',
            'icon' => 'fa-cog',
            'view' => 'larapages::model',
            'model' => 'App\Setting',
            'index' => 'key,value,description',
            'new' => 'Add new setting',
            'new_nl' => 'Instelling toevoegen',
            'columns' => [
                'key' => [
                    'title' => 'Setting',
                    'title_nl' => 'Instelling',
                ],
                'description' => [
                    'title_nl' => 'Omschrijving',
                ],
                'value' => [
                    'title_nl' => 'Waarde',
                ],
            ],
            'orderBy' => 'value',
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
    */
    'role_column' => 'admin_role',
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

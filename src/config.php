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
    | media_path
    |--------------------------------------------------------------------------
    | The path to store media uploads
    */
    'media_path' => public_path('media'),

    /*
    |--------------------------------------------------------------------------
    | media_url
    |--------------------------------------------------------------------------
    | Prefix this to url when using a media item in href or src attributes
    */
    'media_url' => '/media',

    /*
    |--------------------------------------------------------------------------
    | media_upload_limit
    |--------------------------------------------------------------------------
    | Maximum size of an uploaded file in megabytes
    | Still limited by php.ini upload_max_filesize and post_max_size
    */
    'media_upload_limit' => '128',

    /*
    |--------------------------------------------------------------------------
    | media_upload_incremental
    |--------------------------------------------------------------------------
    | If files are uploaded they will replace existing files with the same name
    | When set to true new uploads with the same name will have an incremental
    | value added to the filename, for example img_1.jpg, img_2.jpg, etc.
    */
    'media_upload_incremental' => false,

    /*
    |--------------------------------------------------------------------------
    | media_allowed_extensions
    |--------------------------------------------------------------------------
    | The following file extensions are allowed when uploading
    */
    'media_allowed_extensions' => ['png', 'jpg', 'jpeg', 'gif', 'svg', 'zip', 'pdf', 'doc', 'docx', 'csv', 'xls', 'xlsx', 'pages', 'numbers', 'psd', 'ai', 'eps', 'mp4', 'mp3', 'mpg', 'm4a', 'ogg', 'sketch', 'json', 'rtf', 'md'],

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
            'index' => 'title,date,head,slug,home,menuitem',
            'model' => 'LaraPages\Pages\Page',
            'orderBy' => 'sort',
            'active' => 'active',
            'columns' => [
                'active' => [
                    'default' => true,
                    'title_nl' => 'Actief',
                ],
                'menuitem' => [
                    'default' => true,
                    'index_title' => 'Menu',
                    'index_title_nl' => 'Menu',
                    'title' => 'Show in navigation menu',
                    'title_nl' => 'Toon in navigatie menu',
                ],
                'home' => [
                    'index_title' => 'Home',
                    'index_title_nl' => 'Home',
                    'title' => 'Show on homepage',
                    'title_nl' => 'Toon op homepage',
                ],
                'title' => [
                    'validate' => 'required',
                    'title_nl' => 'Titel',
                ],
                'view' => [
                    'title' => 'View to load (template)',
                    'title_nl' => 'Gebruik template',
                ],
                'head' => [
                    'title_nl' => 'Kop',
                ],
                'html_title' => [
                    'title' => 'HTML Title (for SEO)',
                    'title_nl' => 'HTML Titel (voor SEO)',
                ],
                'slug' => [
                    'placeholder' => 'the url slug, is added to the page url',
                    'placeholder_nl' => 'Unieke \'slug\'',
                ],
                'description' => [
                    'title_nl' => 'Omschrijving',
                ],
                'date' => [
                    'validate' => 'nullable|date',
                    'title_nl' => 'Datum',
                ],
                'pictures' => [
                    'title_nl' => 'Afbeeldingen',
                ],
                'background' => [
                    'title_nl' => 'Achtergrond',
                ],
                'body' => [
                    'title_nl' => 'Inhoud',
                    'tinymce' => [
                        'formats' => "{title: 'Intro', block: 'p', styles: {'font-size':'1.2em', 'margin-bottom':'30px', 'line-height':'1.5em'}},
		                {title: 'H2', block: 'h2'}",
// 		                'css' => '/css/tinymce.css',
// 		                'toolbar' => 'bold italic | link',
                    ],
                    'type' => 'mediumtext',
                ],
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
//                 'Admin user' => 'SELECT id,name,email,created_at,updated_at,admin_role FROM users WHERE admin_role NOT NULL',
                'Settings' => 'SELECT * FROM settings',
            ],
        ],
        'users' => [
            'view' => 'larapages::model',
            'icon' => 'fa-users',
            'title_nl' => 'Gebruikers',
            'model' => 'App\User',
            'columns' => [
                'name' => [
                    'title_nl' => 'Naam',
                    'validate' => 'required',
                ],
                'email' => [
                    'title_nl' => 'E-mailadres',
                    'validate' => 'required|email|unique:users,email,#id#|required',
                ],
                'password' => [
                    'title_nl' => 'Wachtwoord',
                    'type' => 'password',
                    'validate' => 'required|min:8|confirmed',
                ],
                'admin_role' => [
                    'title_nl' => 'Toegangsrechten',
                    'type' => 'roles',
                    'validate' => 'required',
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
            'title_nl' => 'Beheerder',
        ],
        'cms' => [
            'title' => 'Content manager',
            'title_nl' => 'Content bewerker',
            'permissions' => [
                'dashboard' => [ 'read' ],
                'pages' => [ 'create', 'read', 'update' ],
                'media' => [ 'create', 'read', 'update', 'delete' ],
                'settings' => [ 'read', 'update' ],
                'reports' => [ 'read' ],
            ],
        ],
        'demo' => [
            'title' => 'Demo user',
            'title_nl' => 'Demo gebruiker',
            'permissions' => [
                'dashboard' => [ 'read' ],
                'pages' => [ 'read' ],
                'media' => [ 'read' ],
                'settings' => [ 'read' ],
                'reports' => [ 'read' ],
            ],
        ],
    ],

];

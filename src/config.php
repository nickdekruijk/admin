<?php

return [

    /*
    |--------------------------------------------------------------------------
    | adminpath
    |--------------------------------------------------------------------------
    | The url used to login. e.g. 'lp-admin' for www.domain.com/lp-admin
    */
    'adminpath' => 'admin',

    /*
    |--------------------------------------------------------------------------
    | logo
    |--------------------------------------------------------------------------
    | This html code is shown in the upper left corner
    */
    'logo' => '<i class="fa fa-first-order logo"></i>Admin',

    /*
    |--------------------------------------------------------------------------
    | logo_link
    |--------------------------------------------------------------------------
    | The logo should link to this url. Defaults to website root /
    */
    'logo_link' => '/',

    /*
    |--------------------------------------------------------------------------
    | logo_link_target
    |--------------------------------------------------------------------------
    | Target of the logo link. Default _blank (new window/tab)
    */
    'logo_link_target' => '_blank',

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
            'view' => 'admin::dashboard',
            'icon' => 'fa-dashboard',
        ],
        'pages' => [
            'view' => 'admin::model',
            'icon' => 'fa-sitemap',
            'title' => 'Website pages', # If ommited defaults to ucfirst(id)
            'title_nl' => 'Website pagina\'s',
            'button_new' => 'Create new page', # If ommited defaults to 'New'
            'treeview' => 'parent', # Show treeview, 'parent' column name within same model
            'expanded' => 3, # Open treeview upto this depth (default all)
            'sub_navigation' => 'title', # Column to show in subnavigation, only used with treeview and only items with parent 0 or null with children will be shown
            'sub_showall' => true, # When sub_navigation is shown a 'Show all' menu item is added first
            'index' => 'title,date,head,slug,home,menuitem',
            'model' => 'App\Page',
            'orderBy' => 'sort',
            'sortable' => true,
            'active' => 'active',
            'tinymce' => [
//                 'formats' => "{title: 'Intro', block: 'p', styles: {'font-size':'1.2em', 'margin-bottom':'30px', 'line-height':'1.5em'}} , {title: 'H2', block: 'h2'}",
//                 'css' => '/css/tinymce.css',
//                 'toolbar' => 'bold italic | link',
            ],
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
                    'type' => 'text',
                ],
                'date' => [
                    'validate' => 'nullable|date',
                    'title_nl' => 'Datum',
                ],
                'images' => [
                    'title_nl' => 'Afbeeldingen',
                    'type' => 'images',
                ],
                'background' => [
                    'title_nl' => 'Achtergrond',
                    'type' => 'image',
                ],
                'body' => [
                    'title_nl' => 'Inhoud',
                    'tinymce' => true,
                    'type' => 'mediumtext',
                ],
            ],
        ],
        'media' => [
            'view' => 'admin::media',
            'icon' => 'fa-picture-o',
            'expanded' => 3, # Open folders upto this depth (default all)
            'maxUploadSize' => '12', # Maximum size of an uploaded file in megabytes, still limited by php.ini upload_max_filesize and post_max_size
            'folder' => 'media',     # Base folder to store uploaded files. Will be public_path(this)
        ],
        'reports' => [
            'view' => 'admin::reports',
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
            'view' => 'admin::model',
            'icon' => 'fa-users',
            'title_nl' => 'Gebruikers',
            'model' => 'App\User',
            'index' => 'email,name,admin_role',
            'orderBy' => 'created_at',
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
        ],
        'settings' => [
            'view' => 'admin::model',
            'icon' => 'fa-cog',
            'title_nl' => 'Instellingen',
            'model' => 'NickDeKruijk\Settings\Setting',
            'index' => 'key,value,description',
            'new' => 'Add new setting',
            'new_nl' => 'Instelling toevoegen',
            'orderBy' => 'key',
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
                    'type' => 'longtext',
                    'validate' => 'required',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | roles
    |--------------------------------------------------------------------------
    | The roles the users can be assigned to.
    | Must match 'modules' items (see above).
    | Make sure the User model has a column matching the 'role_column' value
    | The 'role_column' of a user determines if a user has access to Admin
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
                'pages' => [ 'create', 'read', 'update', 'delete' ],
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

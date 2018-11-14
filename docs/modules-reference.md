# Modules Reference

Modules are where you define the components of the admin panel. 
All editable models, dashboard, reports and other default modules should be defined in this section of admin.php.
The main navigation will be built with this too.<br/>
For the icons, we are using [FontAwesome](http://fontawesome.io/icons/), e.g.: fa-picture-o


```javascript
'modules' => [
    // Modules definitions here
];
```

# Types of Modules
* [Default Modules](#default-modules)
* [Editable Models](#editable-models)

## Default Modules

These are default internal modules used to build the main options of the admin panel.<br/>
<br/>
For all modules, these attributes are required:
* **view**: The reference to the view used to define the module.
* **icon**: Icon that will appear on the menu.

### Dashboard

```javascript
'dashboard' => [
    'view' => 'admin::dashboard',
    'icon' => 'fa-dashboard',
],
```

### Media

```javascript
'media' => [
    'view' => 'admin::media',
    'icon' => 'fa-picture-o',
    'maxUploadSize' => '12',
    'folder' => 'images',
],
```
* Special attributes
  * **maxUploadSize**: Maximum size of an uploaded file in megabytes, still limited by php.ini upload_max_filesize and post_max_size.
  * **folder**: Base folder to store uploaded files. Will be public_path(this)

### Reports

```javascript
'reports' => [
    'view' => 'admin::reports',
    'icon' => 'fa-print',
    'disabled' => false,
    'download_csv' => true,
    'queries' => [
        'All users' => 'SELECT * FROM users',
    ],
],
```
* Special attributes
  * **download_csv**: Create a button to allow user download the report as a CSV file.
  * **queries**: Create a list of queries that will be the reports you want to provide in the admin for the user. 
  Its format consists in the name of the report and the SQL query that obtains that data.
  
### Users

```javascript
'users' => [
    'view' => 'admin::model',
    'icon' => 'fa-users',
    'title_nl' => 'Gebruikers',
    'model' => 'App\User',
    'index' => 'email,name,admin_role,id',
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
```

## Editable Models

Here is where you can add the models used in your application to become editable inside the admin panel. 
Admin creates a menu entry and the CRUD (create, remove, update, delete) user interface for the model.

[To be continued...]

<?php

namespace LaraPages\Admin\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Support\Facades\View;
use App;

class BaseController extends Controller
{
    // Current user, permissions and navigation is stored in here by __construct
    protected $user;
    // Current slug/module will be stored here
    protected $slug;

    public function __construct()
    {
        // LaraPages requires authentication and a valid role
        $this->middleware(['auth', function($request, $next)  {
            $this->user = $this->checkRole();
            return $next($request);
        }]);
    }

    // Return the items localized title
    public function locale($key, Array $item, $default)
    {
        if (isset($item[$key.'_'.App::getlocale()])) return $item[$key.'_'.App::getlocale()];
        if (isset($item[$key])) return $item[$key];
        return ucfirst(str_replace('_', ' ', $default));
    }

    // Check if authenticated user has a valid role
    public function checkRole($abort = true)
    {
        // Check if User has admin_role column
        if (!isset(Auth::user()[config('larapages.role_column')])) {
            if ($abort) {
                abort(403, 'User has no role ("'.config('larapages.role_column').'" columns missing in model)');
            } else {
                return false;
            }
        }

        $roleId = Auth::user()[config('larapages.role_column')];

        // Check if admin_role matches a valid role
        if (!isset(config('larapages.roles')[$roleId])) {
            if ($abort) {
                abort(403, 'User role "'.$roleId.'" does not exist');
            } else {
                return false;
            }
        }

        $role = config('larapages.roles')[$roleId];
        $role['id'] = $roleId;

        // Create user specific navigation based on role permissions
        $role['modules'] = [];
        foreach(config('larapages.modules') as $id => $module) {
            // Localize title when available
            $module['title'] = $this->locale('title', $module, $id);

            // If 'index' is not set but there are column definitions add them all to index
            if (!isset($module['index']) && isset($module['columns']) && is_array($module['columns'])) {
                $module['index'] = '';
                foreach($module['columns'] as $column_id => $column) {
                    if ($module['index']) $module['index'] .= ',';
                    $module['index'] .= $column_id;
                }
            }

            if (!isset($role['permissions'])) {
                // No permissions defined on role, add navigation item with all permissions
                $role['modules'][$id] = $module;
                $role['modules'][$id]['permissions'] = [ 'create', 'read', 'update', 'delete' ];
            } elseif (isset($role['permissions'][$id])) {
                // User has permissions for this navigation, add it
                $role['modules'][$id] = $module;
                $role['modules'][$id]['permissions'] = $role['permissions'][$id];
            }
        }

        // Unset the config permissions to avoid confusion since permissions are in navigation too
        unset($role['permissions']);

        return $role;
    }

    // Check if user has permission for current module
    public function can($permission)
    {
        return in_array($permission, $this->module('permissions'));
    }

    // Load the view for the current module
    public function show($slug = null)
    {
        // If no slug given fetch the first
        $this->slug = $slug ?: key($this->user['modules']);
        
        // Check if user has this item in navigation, if not then user has no permissions for this or the item does not exist at all. Either way raise 404 error.
        if (!isset($this->user['modules'][$this->slug])) {
            abort(404);
        }

        // Show the view associated with the module and pass the controller and optional message
        $message = null;
        $view = $this->module('view');
        if (!View::exists($view)) {
            $message = 'View '.$view.' '.trans('larapages::base.notfound').'.';
            $view = 'larapages::error';
        }
        
        return view($view, ['lp' => $this, 'message' => $message]);
    }

    // For LaraPages::loginroutes() Facade function
    public function loginroutes()
    {
        return LoginController::routes();
    }

    // Return current loaded module
    public function module($key1 = null, $key2 = null)
    {
        if ($key2) {
            return isset($this->user['modules'][$this->slug][$key1][$key2]) ? $this->user['modules'][$this->slug][$key1][$key2] : [];
        } elseif ($key1) {
            return isset($this->user['modules'][$this->slug][$key1]) ? $this->user['modules'][$this->slug][$key1] : [];
        } else {
            return $this->user['modules'][$this->slug];        
        }
    }

    // Return current users navigation items
    public function navigation()
    {
        // Start output with ul
        $response = '<ul>';
        
        // Add each navigation item the user has access to
        foreach ($this->user['modules'] as $id => $item) {
            $response .= '<li class="'.($id == $this->slug ? 'active' : '').'">';
            $response .= '<a href="'.url(config('larapages.adminpath').'/'.str_slug($id)).'">';
            $response .= '<i class="fa '.$item['icon'].'"></i>';
            $response .= isset($item['title']) ? $item['title'] : ucfirst($id);
            $response .= '</a></li>';
        }
        
        // Add logout 'form'
        $response .= '<li><form id="logout-form" action="'.route('logout').'" method="POST" style="display: none;">'.csrf_field().'</form><a href="'.route('logout').'" onclick="event.preventDefault(); document.getElementById(\'logout-form\').submit();"><i class="fa fa-sign-out"></i>'.trans('larapages::base.logout').'</a></li>';
        
        // Closing <ul>
        $response .= '</ul>';
        
        // Return the html
        return $response;
    }

    // Show the column index for listview header
    public function listviewIndex()
    {
        $response = '';
        foreach (explode(',', $this->module()['index']) as $column) {
            $response .='<span>'.$this->locale('title', $this->module('columns', $column), $column).'</span>';
        }
        return $response;
    }

    // Return an instance of the model
    public function model()
    {
        $model = $this->module('model');
        return class_exists($model) ? new $model : "Model $model not found";
    }

    // Return the listview data formated with <ul>
    public function listviewData()
    {
        $model = $this->model();
        if (is_string($model)) {
            return '<div>'.$model.'</div>';
        }
        if ($this->module('orderBy')) {
            $data = $model::orderBy($this->module('orderBy'))->get();
        } else {
            $data = $model::all();
        }
        $response = '<ul>';
        foreach($data as $row) {
            $response .= '<li><div data-id="'.$row['id'].'"><i></i>';
            foreach (explode(',', $this->module('index')) as $column) {
                $response .='<span>'.$row[$column].'</span>';
            }
            $response .= '</div></li>';
        }
        $response .= '</ul>';
        return $response;
    }
}

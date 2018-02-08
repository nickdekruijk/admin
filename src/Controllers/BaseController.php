<?php

namespace LaraPages\Admin\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Support\Facades\View;
use App;
use Route;

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
            if (!$this->user = $this->checkRole()) {
                Auth::logout();
                return redirect(route('login'))->withErrors(['email' => trans('larapages::base.missing_role')]);
            }
            return $next($request);
        }]);
    }

    // Return the slug
    public function slug()
    {
        return $this->slug;
    }

    // Return the items localized title
    public function locale($key, Array $item, $default)
    {
        if (isset($item[$key.'_'.App::getlocale()])) return $item[$key.'_'.App::getlocale()];
        if (isset($item[$key])) return $item[$key];
        return ucfirst(str_replace('_', ' ', $default));
    }

    // Check if authenticated user has a valid role
    public function checkRole()
    {
        // Check if User has admin_role column
        if (!isset(Auth::user()[config('larapages.role_column')])) {
            return false;
        }

        // Get User roleId from User model based 'role_column' config
        $roleId = Auth::user()[config('larapages.role_column')];

        // Check if admin_role matches a valid role
        if (!isset(config('larapages.roles')[$roleId])) {
            abort(403, 'User role "'.$roleId.'" does not exist');
        }

        // Get the role from config
        $role = config('larapages.roles')[$roleId];

        // Get all modules the user has access to
        $role['modules'] = [];
        foreach(array_merge(config('larapages.modules'), config('larapages.modules2', [])) as $id => $module) {
            // Localize title when available
            $module['title'] = $this->locale('title', $module, $id);

            if (!isset($role['permissions'])) {
                // No permissions defined on role, assume administrator and add module with all permissions
                $role['modules'][$id] = $module;
                $role['modules'][$id]['permissions'] = [ 'create', 'read', 'update', 'delete' ];
            } elseif (isset($role['permissions'][$id])) {
                // User has permissions for this navigation, add it
                $role['modules'][$id] = $module;
                $role['modules'][$id]['permissions'] = $role['permissions'][$id];
            }
        }

        // Unset the config permissions to avoid confusion since permissions are in modules now too
        unset($role['permissions']);

        return $role;
    }

    // Check if user has permission for current module
    public function can($permission)
    {
        return in_array($permission, $this->module('permissions'));
    }

    // Load the view for the current module
    public function view($slug = null)
    {
        $this->checkSlug($slug);
        // Show the view associated with the module and pass the controller and optional message
        $message = null;
        $view = $this->module('view');
        // Return error if view doesn't exist
        if (!View::exists($view)) {
            $message = 'View '.$view.' '.trans('larapages::base.notfound').'.';
            $view = 'larapages::error';
        }
        // Return error if model doesn't exist
        if ($view == 'larapages::model' && !$this->model()) {
            $message = 'Model '.$this->module('model').' '.trans('larapages::base.notfound').'.';
            $view = 'larapages::error';
        }

        return view($view, ['lp' => $this, 'message' => $message]);
    }

    // Check if slug exists and user had permission
    public function checkSlug($slug, $permission = null)
    {
        // If no slug given fetch the first
        $this->slug = $slug ?: key($this->user['modules']);

        // Check if user has this item in navigation, if not then user has no permissions for this or the item does not exist at all. Either way raise 404 error.
        if (!isset($this->user['modules'][$this->slug]) || ($permission && !$this->can($permission))) {
            abort(404);
        }
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
        if (Route::has('logout')) {
            $response .= '<li><form id="logout-form" action="'.route('logout').'" method="POST" style="display: none;">'.csrf_field().'</form><a href="'.route('logout').'" onclick="event.preventDefault(); document.getElementById(\'logout-form\').submit();"><i class="fa fa-sign-out"></i>'.trans('larapages::base.logout').'</a></li>';
        }

        // Closing <ul>
        $response .= '</ul>';

        // Return the html
        return $response;
    }

    // Show the column index for listview header
    public function listviewIndex()
    {
        if ($this->module('index')) {
            $index = explode(',', $this->module('index'));
        } else {
            $index = [];
            foreach($this->columns() as $id => $column) {
                $index[] = $id;
            }
        }
        $response = '';
        foreach ($index as $column) {
            $response .='<span>';
            if ($column == 'id') {
                $response .= 'id';
            } else {
                $response .= $this->locale('index_title', $this->columns($column), false) ?: $this->locale('title', $this->columns($column), $column);
            }
            $response .='</span>';
        }
        return $response;
    }

    // Return an instance of the model
    public function model()
    {
        $model = $this->module('model');
        return class_exists($model) ? new $model : false;
    }

    public function listviewRow($row)
    {
        $response = '<i></i>';
        foreach (explode(',', $this->module('index')) as $column) {
            if ($row[$column] === true) {
                $response .='<span class="center"><i class="fa fa-check"></i></span>';
            } elseif ($this->columns($column, 'type') == 'date') {
                $response .='<span>'.str_replace(' 00:00:00', '', $row[$column]).'</span>';
            } else {
                $response .='<span>'.$row[$column].'</span>';
            }
        }
        return $response;
    }

    // Return the listview data formated with <ul>
    public function listviewData($parent = null)
    {
        // Get model
        $model = $this->model();
        // Does model have treeview then only fetch the children
        if ($this->module('treeview')) {
            $model = $model->where($this->module('treeview'), $parent);
        }
        // Order the results if needed
        if ($this->module('orderBy')) {
            $model = $model->orderBy($this->module('orderBy'));
        }
        // Initialize the response
        $response = '';

        foreach($model->get() as $row) {
            // First row, add <ul>
            if (!$response) $response .= '<ul>';
            $response .= '<li data-id="'.$row['id'].'"'.($this->module('active') && !$row[$this->module('active')]?' class=inactive':'').'><div>';
            $response .= $this->listviewRow($row);
            $response .= '</div>';
            if ($this->module('treeview')) {
                // Add children if any
                $response .= $this->listviewData($row->id);
            }
            $response .= '</li>';
        }
        // Add closing </ul> if there was anything added
        if ($response) $response .= '</ul>';
        return $response;
    }

    // Get the module columns
    public function columns($columnId = null, $index = null)
    {
        $columns = [];
        $model = $this->model();

        foreach($this->module('columns') as $id => $column) {
            if (!is_array($column)) {
                $id = $column;
                $column = [];
            }
            $columns[$id] = $column;
            if (isset($column['type']) && $column['type'] == 'roles') {
                $columns[$id]['type'] = 'select';
                foreach(config('larapages.roles') as $roleId => $role) {
                    $columns[$id]['values'][$roleId] = $this->locale('title', $role, $roleId);
                }
            }
            if (empty($column['type'])) {
                $columns[$id]['type'] = isset($model->getCasts()[$id]) ? $model->getCasts()[$id] : 'string';
            }
            if ($id == $columnId) {
                return $index && isset($columns[$id][$index]) ? $columns[$id][$index] : $columns[$id];
            }
        }
        return $columns;
    }

    // Return the validation rules from the columns
    public function validationRules(Array $replace = [])
    {
        $rules = [];
        foreach ($this->columns() as $columnId => $column) {
            if (isset($column['validate'])) {
                foreach($replace as $replaceKey => $replaceValue) {
                    $column['validate'] = str_replace('#'.$replaceKey.'#', $replaceValue, $column['validate']);
                }
                $rules[$columnId] = $column['validate'];
            }
        }
        return $rules;
    }

    public function browse($return = 'browse')
    {
        return isset($_GET['browse']) && $_GET['browse']=='true'?$return:'';
    }
}

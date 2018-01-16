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
    // Current slug/navigation item will be stored here
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
    private function title(Array $item, $default)
    {
        if (isset($item['title_'.App::getlocale()])) return $item['title_'.App::getlocale()];
        if (isset($item['title'])) return $item['title'];
        return ucfirst($default);
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
        foreach(config('larapages.modules') as $id => $nav) {
            $nav['title'] = $this->title($nav, $id);

            if (!isset($role['permissions'])) {
                // No permissions defined on role, add navigation item with all permissions
                $role['modules'][$id] = $nav;
                $role['modules'][$id]['permissions'] = [ 'create', 'read', 'update', 'delete' ];
            } elseif (isset($role['permissions'][$id])) {
                // User has permissions for this navigation, add it
                $role['modules'][$id] = $nav;
                $role['modules'][$id]['permissions'] = $role['permissions'][$id];
            }
        }
        
        // Unset the config permissions to avoid confusion since permissions are in navigation too
        unset($role['permissions']);
        
        return $role;
    }

    // Load the view for the selected nav item
    public function show($slug = null)
    {
        // If no slug given fetch the first
        $this->slug = $slug ?: key($this->user['modules']);
        
        // Check if user has this item in navigation, if not then user has no permissions for this or the item does not exist at all. Either way raise 404 error.
        if (!isset($this->user['modules'][$this->slug])) {
            abort(404);
        }

        // Show the view associated with the navigation item and pass the controller and optional message
        $message = null;
        $view = $this->user['modules'][$this->slug]['view'];
        if (!View::exists($view)) {
            $message = 'View '.$view.' '.trans('larapages::base.notfound').'.';
            $view = 'larapages::error';
        }
        
        return view($view, ['lp' => $this, 'message' => $message]);
    }

    public function loginroutes()
    {
        return LoginController::routes();
    }
}

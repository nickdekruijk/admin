<?php

namespace NickDeKruijk\Admin\Controllers;

use App;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Route;

class BaseController extends Controller
{
    // Current user, permissions and navigation is stored in here by __construct
    protected $user;
    // Current slug/module will be stored here
    protected $slug;

    public function __construct()
    {
        // Admin requires authentication and a valid role
        $this->middleware(['auth', function ($request, $next) {
            if (!$this->user = $this->checkRole()) {
                Auth::logout();
                return redirect(route('login'))->withErrors(['email' => trans('admin::base.missing_role')]);
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
    public function locale($key, array $item, $default)
    {
        if (isset($item[$key . '_' . App::getlocale()]) && !is_array($item[$key . '_' . App::getlocale()])) {
            return $item[$key . '_' . App::getlocale()];
        }

        if (isset($item[$key]) && !is_array($item[$key])) {
            return $item[$key];
        }

        return ucfirst(str_replace('_', ' ', $default));
    }

    // Check if authenticated user has a valid role
    public function checkRole()
    {
        // Check if User has admin_role column
        if (!isset(Auth::user()[config('admin.role_column')])) {
            return false;
        }

        // Get User roleId from User model based 'role_column' config
        $roleId = Auth::user()[config('admin.role_column')];

        // Check if admin_role matches a valid role
        if (!isset(config('admin.roles')[$roleId])) {
            abort(403, 'User role "' . $roleId . '" does not exist');
        }

        // Get the role from config
        $role = config('admin.roles')[$roleId];

        // Get all modules the user has access to
        $role['modules'] = [];
        foreach (array_merge(config('admin.modules'), config('admin.modules2', [])) as $id => $module) {
            // Localize title when available
            $module['title'] = $this->locale('title', $module, $id);

            if (!isset($role['permissions'])) {
                // No permissions defined on role, assume administrator and add module with all permissions
                $role['modules'][$id] = $module;
                $role['modules'][$id]['permissions'] = ['create', 'read', 'update', 'delete'];
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
            $message = 'View ' . $view . ' ' . trans('admin::base.notfound') . '.';
            $view = 'admin::error';
        }
        // Return error if model doesn't exist
        if ($view == 'admin::model' && !$this->model()) {
            $message = 'Model ' . $this->module('model') . ' ' . trans('base.notfound') . '.';
            $view = 'admin::error';
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
    public function module($key1 = null, $key2 = null, $alt = null)
    {
        if ($key2) {
            return $this->user['modules'][$this->slug][$key1][$key2] ?? $alt[$key2] ?? [];
        } elseif ($key1) {
            return $this->user['modules'][$this->slug][$key1] ?? $alt[$key1] ?? [];
        } else {
            return $this->user['modules'][$this->slug];
        }
    }

    private function navigationLI($active = false, $link = '', $title = null, $icon = null)
    {
        $response = '<li class="' . ($active ? 'active' : '') . '">';
        $response .= '<a href="' . url(config('admin.adminpath') . '/' . $link) . '">';
        if ($icon) {
            $response .= '<i class="fa ' . $icon . '"></i>';
        }
        $response .= $title ?: ucfirst($id);
        $response .= '</a>';
        return $response;
    }

    // Return current users navigation items
    public function navigation()
    {
        // Start output with ul
        $response = '<ul>';

        // Add each navigation item the user has access to
        foreach ($this->user['modules'] as $id => $item) {
            $response .= $this->navigationLI($id == $this->slug, Str::slug($id), $item['title'], $item['icon']);
            if (isset($item['sub_navigation']) && isset($item['treeview']) && class_exists($item['model'])) {
                $data = new $item['model'];
                $data = $this->sortModel($data, @$item['orderByDesc'], 'desc');
                $data = $this->sortModel($data, @$item['orderBy']);
                $data = $data->whereNull($item['treeview']);
                if (isset($item['active'])) {
                    $data = $data->where($item['active'], 1);
                }
                $subresponse = '<ul>';
                $count = 0;
                foreach ($data->get() as $subitem) {
                    if ((new $item['model'])->where($item['treeview'], $subitem->id)->count()) {
                        $count++;
                        if ($count == 1 && isset($item['sub_showall']) && $item['sub_showall']) {
                            $subresponse .= $this->navigationLI($id == $this->slug && !request()->root, Str::slug($id), $item['sub_showall'] === true ? trans('admin::base.showall') : $this->locale('sub_showall', $item, false)) . '</li>';
                        }
                        $subresponse .= $this->navigationLI($id == $this->slug && request()->root == $subitem->id, Str::slug($id) . '?root=' . $subitem->id, $subitem[$item['sub_navigation']]) . '</li>';
                    }
                }
                $subresponse .= '</ul>';
                if ($count) {
                    $response .= $subresponse;
                }
            }
            $response .= '</li>';
        }

        // Add logout 'form'
        if (Route::has('logout')) {
            $response .= '<li><form id="logout-form" action="' . route('logout') . '" method="POST" style="display: none;">' . csrf_field() . '</form><a href="' . route('logout') . '" onclick="event.preventDefault(); document.getElementById(\'logout-form\').submit();"><i class="fa fa-sign-out"></i>' . trans('admin::base.logout') . '</a></li>';
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
            foreach ($this->columns() as $id => $column) {
                $index[] = $id;
            }
        }
        if ($this->module('index_filters')) {
            $filters = explode(',', $this->module('index_filters'));
        } else {
            $filters = [];
        }
        $response = '';
        foreach ($index as $n => $column) {
            $canfilter = in_array($column, $filters);
            $column = explode('.', $column);
            $column = ($this->columns($column[0], 'type') == 'array' && isset($column[1])) ? $column[1] : $column[0];
            if ($canfilter) {
                $response .= '<span data-column="' . $n . '" class="canfilter">';
            } else {
                $response .= '<span>';
            }
            if ($column == 'id') {
                $response .= 'id';
            } else {
                $response .= $this->locale('index_title', $this->columns($column), false) ?: $this->locale('title', $this->columns($column), $column);
            }
            if ($canfilter) {
                $response .= '<i class="fa fa-filter"></i><ul></ul>';
            }
            $response .= '</span>';
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
        $response = $this->module('treeview') ? '<i></i>' : '';
        foreach (explode(',', $this->module('index')) as $column) {
            if ($row[$column] === true) {
                $response .= '<span><i class="fa fa-check"></i></span>';
            } elseif ($this->columns($column, 'type') == 'pivot') {
                $value = '';
                foreach ($row[$column] as $opt) {
                    $value .= ($value ? '; ' : '') . $this->getModelDataColumns($this->columns($column), $opt);
                }
                $response .= '<span>' . $value . '</span>';
            } elseif ($this->columns($column, 'type') == 'date') {
                $response .= '<span>' . str_replace(' 00:00:00', '', $row[$column]) . '</span>';
            } elseif ($this->columns($column, 'type') == 'select' && isset($this->columns($column, 'values')[$row[$column]])) {
                $value = $this->columns($column, 'values')[$row[$column]];
                if (is_array($value)) {
                    $value = $value['value'] ?? $row[$column];
                }
                $response .= '<span>' . $value . '</span>';
            } else {
                unset($value);
                foreach (explode('.', $column) as $s) {
                    $value = $value[$s] ?? $row[$s];
                    if (is_null($value)) {
                        break;
                    }
                }
                $response .= '<span>' . htmlspecialchars($value) . '</span>';
            }
        }
        return $response;
    }

    private function sortModel($model, $columns, $direction = 'asc')
    {
        if ($columns) {
            if (!is_array($columns)) {
                $columns = explode(',', trim($columns));
            }
            foreach ($columns as $column) {
                $column = explode(' ', $column, 2);
                if (empty($column[1])) {
                    $column[1] = $direction;
                }
                $model = $model->orderBy(trim($column[0]), $column[1]);
            }
        }
        return $model;
    }

    // Return the listview data formated with <ul>
    public function listviewData($parent = null, $depth = 0)
    {
        // Get model
        $model = $this->model();
        // Does model have treeview then only fetch the children
        if ($this->module('treeview')) {
            $model = $model->where($this->module('treeview'), $parent);
        }
        // Add with() if needed
        if ($this->module('with')) {
            foreach (explode(',', $this->module('with')) as $with) {
                $model = $model->with(trim($with));
            }
        }
        // Order the results if needed
        $model = $this->sortModel($model, $this->module('orderBy'));
        $model = $this->sortModel($model, $this->module('orderByDesc'), 'desc');
        // Initialize the response
        $response = '';

        foreach ($model->get() as $row) {
            // First row, add <ul>
            if (!$response) {
                $response .= '<ul' . ($this->module('expanded') > 0 && $this->module('expanded') <= $depth ? ' class="closed"' : '') . '>';
            }

            $response .= '<li data-id="' . $row['id'] . '"' . ($this->module('active') && !$row[$this->module('active')] ? ' class=inactive' : '') . '>';
            if ($this->module('treeview')) {
                $response .= '<div>' . $this->listviewRow($row) . '</div>';
                // Add children if any
                $response .= $this->listviewData($row->id, $depth + 1);
            } else {
                $response .= $this->listviewRow($row);
            }
            $response .= '</li>';
        }
        // Add closing </ul> if there was anything added
        if ($response) {
            $response .= '</ul>';
        }

        return $response;
    }

    // Get the module columns
    public function columns($columnId = null, $index = null)
    {
        $columns = [];
        $model = $this->model();

        foreach ($this->module('columns') as $id => $column) {
            if (!is_array($column)) {
                $id = $column;
                $column = [];
            }
            $columns[$id] = $column;
            if (isset($column['type']) && $column['type'] == 'roles') {
                $columns[$id]['type'] = 'select';
                foreach (config('admin.roles') as $roleId => $role) {
                    $columns[$id]['values'][$roleId] = $this->locale('title', $role, $roleId);
                }
            }
            if (empty($column['type'])) {
                $columns[$id]['type'] = isset($model->getCasts()[$id]) ? $model->getCasts()[$id] : (isset($column['tinymce']) ? 'text' : 'string');
            }
            if ($id == $columnId) {
                return $index && isset($columns[$id][$index]) ? $columns[$id][$index] : $columns[$id];
            }
        }
        return $columns;
    }

    // Return the validation rules from the columns
    public function validationRules(array $replace = [])
    {
        $rules = [];
        foreach ($this->columns() as $columnId => $column) {
            if (isset($column['validate'])) {
                foreach ($replace as $replaceKey => $replaceValue) {
                    $column['validate'] = str_replace('#' . $replaceKey . '#', $replaceValue, $column['validate']);
                }
                $rules[$columnId] = $column['validate'];
            }
        }
        return $rules;
    }

    public function browse($return = 'browse')
    {
        return isset($_GET['browse']) && $_GET['browse'] == 'true' ? $return : '';
    }

    private function foreign_walk($column, $parent = null, $depth = 0)
    {
        $response = '';
        $data = $this->getModelData($column);
        if (isset($column['treeview'])) {
            $data = $data->where($column['treeview'], $parent);
        }
        foreach ($data->get() as $opt) {
            $response .= '<option value="' . $opt['id'] . '">';
            $response .= str_repeat('&nbsp', $depth * 4);
            $response .= $this->getModelDataColumns($column, $opt);
            $response .= '</option>';
            if (isset($column['treeview']) && (!isset($column['maxdepth']) || $column['maxdepth'] > $depth)) {
                $response .= $this->foreign_walk($column, $opt['id'], $depth + 1);
            }
        }
        return $response;
    }

    // Return the <select> tree for a foreign relationship
    public function foreign($columnId, $column, $showId = true)
    {
        $response = '<select name="' . $columnId . '"';
        if ($showId) {
            $response .= ' id="input_' . $columnId . '"';
        } else {
            $response .= ' data-column="' . str_replace('[]', '', $columnId) . '"';
        }
        $response .= '>';
        $response .= '<option value=""></option>';
        $response .= $this->foreign_walk($column);
        $response .= '</select>';
        return $response;
    }

    private function getModelData($column)
    {
        if (isset($column['scope'])) {
            $scope = $column['scope'];
            $data = (new $column['model'])->$scope();
        } else {
            $data = (new $column['model']);
            if (isset($column['orderby'])) {
                $data = $data->orderBy($column['orderby']);
            }
        }
        return $data;
    }

    private function getModelDataColumns($column, $opt)
    {
        $response = '';
        if (isset($column['columns'])) {
            foreach (explode(',', $column['columns']) as $n => $col) {
                if ($n) {
                    $response .= ', ';
                }
                foreach (explode('.', $col) as $s) {
                    $value = $value[$s] ?? $opt[$s];
                }
                $response .= $value;
            }
        } else {
            $response .= implode(', ', $opt->toArray());
        }
        return $response;
    }

    private function pivot_walk($columnId, $column, $data, $parent = 0, $depth = 0)
    {
        $response = '';
        foreach ($data[$parent] as $opt) {
            $response .= $this->pivot_label($columnId, $column, $opt);
            if (isset($data[$opt->id])) {
                $response .= '<div class="pivot-depth ' . ($depth + 1) . '">' . $this->pivot_walk($columnId, $column, $data, $opt->id, $depth + 1) . '</div>';
            }
        }
        return $response;
    }

    private function pivot_label($columnId, $column, $opt)
    {
        return '<label class="pivot' . (($column['treeview'] ?? false) ? ' treeview' : '') . (!empty($column['active']) && !$opt[$column['active']] ? ' inactive' : '') . '"><input type="checkbox" class="pivot-' . $columnId . '" name="' . $columnId . '[]" value="' . $opt->id . '"><span>' . $this->getModelDataColumns($column, $opt) . '</span></label>';
    }

    // Return all labels for a many to many (pivot) relationship
    public function pivot($columnId, $column)
    {
        $response = '';
        $data = $this->getModelData($column);
        if ($column['treeview'] ?? false) {
            return $this->pivot_walk($columnId, $column, $data);
        } else {
            foreach ($data->get() as $opt) {
                $response .= $this->pivot_label($columnId, $column, $opt);
            }
        }
        return $response;
    }

    // Return the line input for a many to many (pivot) relationship
    public function rows($columnId, $column)
    {
        $data = $this->getModelData($column);
        $response = '<table class="rows" id="input_' . $columnId . '">';
        $response .= '<tr class="template">';
        foreach ($column['columns'] as $columnId2 => $opt) {
            $response .= '<td>';
            if (empty($opt['type'])) {
                $opt['type'] = 'string';
            }

            if ($opt['type'] == 'string' || $opt['type'] == 'password' || $opt['type'] == 'date' || $opt['type'] == 'datetime' || $opt['type'] == 'number') {
                $response .= '<input class="' . ($opt['type'] == 'date' ? 'datepicker' : '') . ($opt['type'] == 'datetime' ? 'datetimepicker' : '') . '" type="' . ($opt['type'] == 'string' || $opt['type'] == 'date' || $opt['type'] == 'datetime' ? 'text' : $opt['type']) . '" name="' . $columnId . '_' . $columnId2 . '[]" data-column="' . $columnId . '_' . $columnId2 . '" placeholder="' . $this->locale('placeholder', $opt, '') . '">';
            } elseif ($opt['type'] == 'foreign') {
                $response .= $this->foreign($columnId . '_' . $columnId2 . '[]', $opt, false);
            } else {
                $response .= $opt['type'];
            }
            $response .= '</td>';
        }
        if ($this->can('delete')) {
            $response .= '<td><button type="button" data-confirm="' . trans('admin::base.deleteconfirm') . '" class="pivot-delete button is-red"><i class="fa fa-trash"></i></button></td>';
        }
        $response .= '</tr>';
        $response .= '<tr>';
        foreach ($column['columns'] as $col => $opt) {
            $response .= '<th>' . $this->locale('title', $opt, $col) . '</th>';
        }
        $response .= '</tr>';
        $response .= '</table>';
        return $response;
    }
}

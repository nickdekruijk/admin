<?php

namespace NickDeKruijk\Admin\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use NickDeKruijk\Admin\Helpers;
use NickDeKruijk\Admin\Middleware\Admin;
use NickDeKruijk\Admin\Models\Permission;

class AdminController extends Controller
{
    public array $modules;
    public string $component;
    public $module;
    public string $slug;

    public function __construct()
    {
        Auth::shouldUse(config('admin.guard'));
        $this->middleware(Admin::class, ['except' => ['login']]);
    }

    /**
     * Return all modules the current user has access to
     *
     * @return array
     */
    public function getAllModules(): array
    {
        if (!isset($this->modules)) {
            // Get all modules the current user has any permission for.
            foreach (Permission::any()->currentUser()->get() as $permission) {
                if ($permission->module == '*') {
                    // User has access to all modules.
                    $all_modules = config('admin.modules');
                } else {
                    $all_modules[] = $permission->module;
                }
            }

            // From the available modules, get the ones that are actually available and create instance.
            foreach ($all_modules as $module) {
                if (class_exists($module)) {
                    $modules[] = new $module;
                }
            }
            $this->modules = $modules;
        }
        return $this->modules;
    }

    public function index($slug = null)
    {
        foreach ($this->getAllModules() as $module) {
            if ($module->getAdminConfig()->slug === $slug || !$slug) {
                $this->component = $module->getAdminConfig()->component;
                $this->module = $module;
                $this->slug = $slug ?: $module->getAdminConfig()->slug;
                return view('admin::layouts.app', ['admin' => $this]);
            }
        }
        abort(404);
    }

    public function login()
    {
        $this->component = 'admin.login';
        return view('admin::layouts.app', ['admin' => $this]);
    }

    public function logout()
    {
        Auth::logout();
        request()->session()->regenerateToken();
        return redirect()->route('admin.index');
    }
}

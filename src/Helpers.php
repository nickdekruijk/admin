<?php

namespace NickDeKruijk\Admin;

use Illuminate\Support\Facades\Auth;
use NickDeKruijk\Admin\Models\Permission;

class Helpers
{
    /**
     * Get the user model instance from admin config
     *
     * @return User;
     */
    public static function userModel()
    {
        Auth::shouldUse(config('admin.guard'));
        $model = Auth::getProvider()->getModel();
        return new $model;
    }

    /**
     * Return all modules the current user has access to
     *
     * @return array
     */
    public static function getAllModules(): array
    {
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
        return $modules;
    }

    /**
     * Find a module by the slug and return an instance of it.
     * If no slug is given, return the first module the user is allowed to access.
     *
     * @param string|null $slug
     * @return mixed
     */
    public static function getModule(string $slug = null): mixed
    {
        foreach (Helpers::getAllModules() as $module) {
            if ($module->getAdminConfig()->slug === $slug || !$slug) {
                return new $module;
            }
        }
    }

    /**
     * Find a module or return 404 if it doesn't exist or user doesn't have permissions.
     *
     * @param string $slug
     * @return mixed
     */
    public static function getModuleOrFail(string $slug = null): mixed
    {
        return Helpers::getModule($slug) ?? abort(404);
    }
}

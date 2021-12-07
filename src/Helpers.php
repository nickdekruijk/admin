<?php

namespace NickDeKruijk\Admin;

use Illuminate\Support\Facades\Auth;

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

    public static function getAllModules()
    {
        foreach (config('admin.modules') as $module) {
            if (class_exists($module)) {
                $modules[] = new $module;
            }
        }
        return $modules;
    }

    public static function getModule($slug = null)
    {
        foreach (Helpers::getAllModules() as $module) {
            if ($module->getAdminConfig()->slug === $slug || !$slug) {
                return new $module;
            }
        };
    }

    public static function getModuleOrFail($slug)
    {
        return Helpers::getModule($slug) ?? abort(404);
    }
}

<?php

namespace NickDeKruijk\Admin;

use App\Models\User;
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

    public static function can(string $module, string $permission = null, User $user = null): bool
    {
        $permissions = Permission::where(function ($query) use ($module) {
            $query->where('module', '*')->orWhere('module', $module);
        })->where('user_id', $user ? $user->id : Auth::user()->id);
        if ($permission) {
            return $permissions->where($permission, true)->count();
        } else {
            return $permissions->where(function ($query) {
                $query->where('create', true)
                    ->orWhere('read', true)
                    ->orWhere('update', true)
                    ->orWhere('delete', true);
            })->count();
        }
    }

    public static function getAllModules()
    {
        $modules = [];
        foreach (config('admin.modules') as $module) {
            if (class_exists($module) && Helpers::can($module)) {
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

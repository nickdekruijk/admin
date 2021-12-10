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
}

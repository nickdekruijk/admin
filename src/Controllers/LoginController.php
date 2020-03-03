<?php

namespace NickDeKruijk\Admin\Controllers;

class LoginController extends \App\Http\Controllers\Auth\LoginController
{
    /**
     * Where to redirect users after login.
     *
     * Change the value from \App\Http\Controllers\Auth\LoginController to the admin.adminpath config
     */
    public function __construct()
    {
        $this->redirectTo = '/' . config('admin.adminpath');
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        return view('admin::login');
    }
}

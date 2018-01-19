<?php

namespace LaraPages\Admin\Controllers;

use Route;

class LoginController extends \App\Http\Controllers\Auth\LoginController
{
    /**
     * Where to redirect users after login.
     *
     * Change the value from \App\Http\Controllers\Auth\LoginController to the larapages.adminpath config
     */
    public function __construct()
    {
        $this->redirectTo = '/'.config('larapages.adminpath');
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        return view('larapages::login');
    }

}

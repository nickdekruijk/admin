<?php

namespace LaraPages\Admin\Controllers;

use Route;

class LoginController extends \App\Http\Controllers\Auth\LoginController
{
    /**
     * Set the LaraPages authentication routes.
     *
     * This is called by the LaraPages::loginroutes() Facade function
     */
    public static function routes()
    {
        Route::get(config('larapages.adminpath').'/login', '\LaraPages\Admin\Controllers\LoginController@showLoginForm')->name('login');
        Route::post(config('larapages.adminpath').'/login', '\LaraPages\Admin\Controllers\LoginController@login');
        Route::post(config('larapages.adminpath').'/logout', '\LaraPages\Admin\Controllers\LoginController@logout')->name('logout');
    }

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

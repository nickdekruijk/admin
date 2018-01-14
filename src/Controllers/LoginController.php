<?php

namespace LaraPages\Admin\Controllers;

use Route;

class LoginController extends \App\Http\Controllers\Auth\LoginController
{
    static public function routes()
    {
        Route::get(config('larapages.adminpath').'/login', '\LaraPages\Admin\Controllers\LoginController@showLoginForm')->name('login');
        Route::post(config('larapages.adminpath').'/login', '\LaraPages\Admin\Controllers\LoginController@login');
        Route::post(config('larapages.adminpath').'/logout', '\LaraPages\Admin\Controllers\LoginController@logout')->name('logout');
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

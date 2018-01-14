<?php

namespace NickDeKruijk\LaraPages\Controllers;

use Route;

class LoginController extends \App\Http\Controllers\Auth\LoginController
{
    static public function routes()
    {
        Route::get(config('larapages.adminpath').'/login', '\NickDeKruijk\LaraPages\Controllers\LoginController@showLoginForm')->name('login');
        Route::post(config('larapages.adminpath').'/login', '\NickDeKruijk\LaraPages\Controllers\LoginController@login');
        Route::post(config('larapages.adminpath').'/logout', '\NickDeKruijk\LaraPages\Controllers\LoginController@logout')->name('logout');
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

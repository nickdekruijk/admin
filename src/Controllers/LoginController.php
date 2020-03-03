<?php

namespace NickDeKruijk\Admin\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * Change the value from \App\Http\Controllers\Auth\LoginController to the admin.adminpath config
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
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

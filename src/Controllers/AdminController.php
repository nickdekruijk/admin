<?php

namespace NickDeKruijk\Admin\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use NickDeKruijk\Admin\Helpers;
use NickDeKruijk\Admin\Middleware\Admin;

class AdminController extends Controller
{
    public function __construct()
    {
        Auth::shouldUse(config('admin.guard'));
        $this->middleware(Admin::class, ['except' => ['login']]);
    }

    public function index($slug = null)
    {
        return view('admin::layouts.app', ['module' => Helpers::getModuleOrFail($slug), 'slug' => $slug ?: Helpers::getModule()->getAdminConfig()->slug]);
    }

    public function login()
    {
        return view('admin::layouts.app');
    }

    public function logout()
    {
        Auth::logout();
        request()->session()->regenerateToken();
        return redirect()->route('admin.index');
    }
}

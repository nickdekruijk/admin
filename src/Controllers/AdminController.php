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
        $this->middleware(Admin::class);
    }

    public function index($slug = null)
    {
        $module = Helpers::getModule($slug);
        abort_if(!$module, 404);
        $config = $module->getAdminConfig();
        return view('admin::layouts.app', ['component' => $config->component]);
    }

    public function logout()
    {
        Auth::logout();
        request()->session()->regenerateToken();
        return redirect()->route('admin.index');
    }
}

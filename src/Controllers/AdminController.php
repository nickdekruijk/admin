<?php

namespace NickDeKruijk\Admin\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use NickDeKruijk\Admin\Livewire\AdminDashboard;
use NickDeKruijk\Admin\Middleware\Admin;

class AdminController extends Controller
{
    public function __construct()
    {
        Auth::shouldUse(config('admin.guard'));
        $this->middleware(Admin::class);
    }

    public static function nav(): string
    {
        function walk(array $nav, int $depth = 0): string
        {
            $html = '<ul>';
            foreach ($nav as $key => $value) {
                $html .= '<li>';
                $html .= '<a href="">' . __($key) . '</a>';
                if (is_array($value)) {
                    $html .= walk($value, $depth + 1);
                }
                $html .= '</li>';
            }
            if ($depth == 0) {
                $html .= '<li><form method="post" action="' . route('admin.logout') . '" onclick="this.submit()">' . csrf_field() . __('Logout') . '</form></li>';
            }
            $html .= '</ul>';
            return $html;
        }
        return walk(config('admin.components'));
    }

    public function index()
    {
        return AdminDashboard::render();
    }

    public function logout()
    {
        Auth::logout();
        request()->session()->regenerateToken();
        return redirect()->route('admin.index');
    }
}

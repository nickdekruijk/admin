<?php

namespace NickDeKruijk\Admin\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    public $email;
    public $password;

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required',
    ];

    protected function rules()
    {
        $rules = [];
        foreach (config('admin.credentials') as $column) {
            if ($column == 'email') {
                $rules[$column] = 'required|email';
            } else {
                $rules[$column] = 'required';
            }
        }
        return $rules;
    }

    public function submit()
    {
        $this->validate();
        $credentials = [];
        foreach (config('admin.credentials') as $column) {
            $credentials[$column] = $this->$column;
        }
        Auth::shouldUse(config('admin.guard'));
        if (Auth::attempt($credentials)) {
            request()->session()->regenerate();
            return redirect()->intended(route('admin.index'));
        } else {
            $this->addError('login', trans('auth.failed'));
        }
    }

    public static function render()
    {
        config(['livewire.layout' => 'admin::layouts.app']);
        return view('admin::livewire.login');
    }
}

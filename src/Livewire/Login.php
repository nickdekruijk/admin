<?php

namespace NickDeKruijk\Admin\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use NickDeKruijk\Admin\Traits\AdminModule;

class Login extends Component
{
    use AdminModule;

    public $email;
    public $password;

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required',
    ];

    public function __construct()
    {
        Auth::shouldUse(config('admin.guard'));
    }

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
        if (Auth::attempt($credentials)) {
            return $this->redirectIntended();
        } else {
            $this->addError('login', trans('auth.failed'));
        }
    }

    private function redirectIntended()
    {
        request()->session()->regenerate();
        return redirect()->intended(route('admin.index'));
    }

    public function mount()
    {
        if (Auth::check()) {
            return $this->redirectIntended();
        }
    }

    public static function render()
    {
        config(['livewire.layout' => 'admin::layouts.app']);
        return view('admin::livewire.login');
    }
}

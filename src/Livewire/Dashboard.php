<?php

namespace NickDeKruijk\Admin\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use NickDeKruijk\Admin\Traits\AdminModule;

class Dashboard extends Component
{
    use AdminModule;

    public string $greeting = '';

    public function mount()
    {
        // Determine evening, afternoon or morning.
        $hour = date('H');
        $this->greeting = __('Good ' . (($hour >= 18) ? "evening" : (($hour >= 12) ? "afternoon" : "morning")));

        // Append users first name if present, else take first word from full name.
        $this->greeting .= ' ' . (Auth::user()->firstname ?: Auth::user()->first_name ?: explode(' ', Auth::user()->name)[0]) . '!';
    }

    public static function render()
    {
        return view('admin::livewire.dashboard');
    }
}

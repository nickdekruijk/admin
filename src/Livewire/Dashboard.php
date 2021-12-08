<?php

namespace NickDeKruijk\Admin\Livewire;

use Livewire\Component;
use NickDeKruijk\Admin\Traits\AdminModule;

class Dashboard extends Component
{
    use AdminModule;


    public string $greeting = '';

    public function mount($module = null)
    {
        $this->admin_config = $module->getAdminConfig();
        $hour = date('H');
        $this->greeting = __('Good ' . (($hour >= 18) ? "evening" : (($hour >= 12) ? "afternoon" : "morning")));
    }

    public static function render()
    {
        return view('admin::livewire.dashboard');
    }
}

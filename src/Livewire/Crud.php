<?php

namespace NickDeKruijk\Admin\Livewire;

use Livewire\Component;
use NickDeKruijk\Admin\Traits\AdminModule;

class Crud extends Component
{
    use AdminModule;

    public function mount($admin = null)
    {
        $this->admin_config = $admin->module->getAdminConfig();
    }

    public static function render()
    {
        return view('admin::livewire.crud');
    }
}

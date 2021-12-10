<?php

namespace NickDeKruijk\Admin\Livewire;

use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use NickDeKruijk\Admin\Traits\AdminModule;

class Crud extends Component
{
    use AdminModule;

    public $module;
    public $listview;

    public function mount($admin)
    {
        Gate::authorize('admin.any', $this);

        $this->listview = $admin->module->getAdminConfig()->getListview() ?: $admin->module->getFillable();
        $this->module = $admin->module;
        $this->admin_config = $admin->module->getAdminConfig();
    }

    public static function render()
    {
        return view('admin::livewire.crud');
    }
}

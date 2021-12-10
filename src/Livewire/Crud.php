<?php

namespace NickDeKruijk\Admin\Livewire;

use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Crud extends Component
{
    public $module;
    public $listview;

    public function mount($admin)
    {
        Gate::authorize('admin.any', $this);

        $this->listview = $admin->module->getAdminConfig()->getListview() ?: $admin->module->getFillable();
        $this->module = $admin->module;
    }

    public static function render()
    {
        return view('admin::livewire.crud');
    }
}

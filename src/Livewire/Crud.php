<?php

namespace NickDeKruijk\Admin\Livewire;

use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Crud extends Component
{
    public $module;
    public $listview;
    public $editing = false;
    public $data;

    public function mount($admin)
    {
        Gate::authorize('admin.any', $this);

        // Get columns for listview, if not set, use all fillable columns except hidden.
        $this->listview = $admin->module->getAdminConfig()->getListview() ?: array_diff($admin->module->getFillable(), $admin->module->getHidden());

        // Save module so Livewire can access it.
        $this->module = $admin->module;
    }

    public static function render()
    {
        return view('admin::livewire.crud');
    }

    public function create()
    {
        $this->editing = 0;
    }

    public function edit($id)
    {
        $this->data = $this->module->findOrFail($id);
        $this->editing = $id;
    }

    public function close()
    {
        $this->editing = false;
    }
}

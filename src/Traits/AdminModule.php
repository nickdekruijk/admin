<?php

namespace NickDeKruijk\Admin\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use NickDeKruijk\Admin\Classes\AdminConfig;

trait AdminModule
{
    private AdminConfig $admin_config;

    public function __construct()
    {
        parent::__construct();

        // Create new AdminConfig instance and set title based on class name.
        $this->admin_config = new AdminConfig();
        $this->admin_config->title = class_basename($this);

        // If the module is a Model use the plural of the model name as the title and use CRUD component other wise use the title for the component.
        if ($this instanceof Model) {
            $this->admin_config->component = 'admin.crud';
            $this->admin_config->title = Str::plural($this->admin_config->title);
        } else {
            $this->admin_config->component = 'admin.' . Str::slug($this->admin_config->title);
        }

        // Determine slug and use it for default icon.
        $this->admin_config->slug = Str::slug($this->admin_config->title);
        $this->admin_config->icon = 'fa-solid fa-' . $this->admin_config->slug;
    }

    public function getAdminConfig()
    {
        return $this->admin_config;
    }
}

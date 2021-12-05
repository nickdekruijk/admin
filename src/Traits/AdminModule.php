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

        $this->admin_config = new AdminConfig();
        $this->admin_config->title = class_basename($this);
        $this->admin_config->slug = Str::slug($this->admin_config->title);
        $this->admin_config->icon = 'fa-solid fa-' . $this->admin_config->slug;
        $this->admin_config->component = 'admin.' . $this->admin_config->slug;

        if ($this instanceof Model) {
            $this->admin_config->component = 'admin.crud';
            $this->admin_config->title = Str::plural($this->admin_config->title);
        }
    }

    public function getAdminConfig()
    {
        return $this->admin_config;
    }
}

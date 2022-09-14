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

        // Create new AdminConfig instance.
        $this->admin_config = new AdminConfig($this);
    }

    public function getAdminConfig()
    {
        return $this->admin_config;
    }
}

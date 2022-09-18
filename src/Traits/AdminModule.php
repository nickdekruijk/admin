<?php

namespace NickDeKruijk\Admin\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use NickDeKruijk\Admin\Classes\AdminConfig;

trait AdminModule
{
    private $admin_config;

    // public function __construct()
    // {
    //     parent::__construct();

    //     // Create new AdminConfig instance.
    //     $this->admin_config = new AdminConfig($this);

    //     // If the Class that uses the AdminModule trait has a $adminConfig property merge them
    //     $this->setAdminConfig();

    //     // If the Class that uses the AdminModule trait has a $adminConfig property merge them
    //     if (isset($this->adminConfig)) {
    //         foreach ($this->adminConfig as $key => $value) {
    //             $this->admin_config->$key = $value;
    //         }
    //     }
    // }

    /**
     * Returns the complete AdminConfig object or one of its keys
     *
     * @param string|null $key  The AdminConfig key to return, use null to return everything.
     * @return AdminConfig|string|array
     */
    public function getAdminConfig(string $key = null): AdminConfig|string|array
    {
        if (!$this->admin_config) {
            $this->admin_config = new AdminConfig($this);
        }
        return $key ? $this->admin_config->$key : $this->admin_config;
    }

    /**
     * Classes that use the AdminModule trait can overwrite this function to update config
     *
     * @return void
     */
    public function setAdminConfig(): void
    {
        // $this->admin_config->title = 'New title';
    }

    /**
     * Merge the current AdminConfig object with new and/or updated attributes.
     * Useful for usage in overwriting the setAdminConfig function.
     *
     * @param array $attributes
     * @return AdminConfig
     */
    public function mergeAdminConfig(array $attributes = []): AdminConfig
    {
        $config = $this->admin_config;

        foreach ($attributes as $key => $value) {
            $config->$key = $value;
        }

        return $config;
    }
}

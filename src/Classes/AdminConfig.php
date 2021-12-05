<?php

namespace NickDeKruijk\Admin\Classes;

use Illuminate\Contracts\Support\Arrayable;

class AdminConfig implements Arrayable
{
    /**
     * The title for the menu item.
     *
     * @var string
     */
    public string $title;

    /**
     * The font awesome icon for the menu item, e.g. 'fa-solid fa-dashboard'.
     *
     * @var string
     */
    public string $icon;

    /**
     * Thw url slug that will identify the module.
     *
     * @var string
     */
    public string $slug;

    /**
     * The livewire component to render.
     *
     * @var string
     */
    public string $component;

    /**
     * THe columns that will be editable with the CRUD component.
     *
     * @var array
     */
    public array $crudColumns = [];

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return (array) $this;
    }
}

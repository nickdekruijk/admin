<?php

namespace NickDeKruijk\Admin\Classes;

use ArrayAccess;
use Illuminate\Contracts\Support\Arrayable;

class AdminConfig implements Arrayable, ArrayAccess
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
    public ?string $icon;

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
     * The title of the navigation group the module should belong to.
     *
     * @var string|null
     */
    public ?string $group;

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

    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            $this->$key = $value;
        }
    }

    // Required for ArrayAccess
    public function offsetSet($offset, $value): void
    {
        $this->$offset = $value;
    }
    public function offsetExists($offset): bool
    {
        return isset($this->$offset);
    }
    public function offsetUnset($offset): void
    {
        unset($this->$offset);
    }
    public function offsetGet($offset)
    {
        return $this->$offset ?? null;
    }
}

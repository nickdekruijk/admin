<?php

namespace NickDeKruijk\Admin\Classes;

use ArrayAccess;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AdminConfig implements Arrayable, ArrayAccess
{
    /**
     * The title for the menu item.
     *
     * @var string
     */
    public string $title;

    /**
     * The font-awesome 6.0 icon for the menu item, e.g. 'fa-solid fa-dashboard'.
     *
     * @var string
     */
    public ?string $icon;

    /**
     * The url slug that will identify the module.
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
     * The columns that will be show in the CRUD component listview, e.g. ['name', 'email'] or 'name,email'.
     * Can only be accessed with getListview() method.
     *
     * @var array|string
     */
    public array|string $listview = [];

    /**
     * The columns that will be editable with the CRUD component.
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

    public function getListview()
    {
        return is_array($this->listview) ? $this->listview : explode(',', $this->listview);
    }

    public function __construct(object $module, array $attributes = [])
    {
        // Create default config based on class name.
        $this->title = class_basename(is_string($module) ? $module : $module::class);

        // If the module is a Model use the plural of the model name as the title and use CRUD component other wise use the title for the component.
        if ($module instanceof Model) {
            $this->component = 'admin.crud';
            $this->title = Str::plural($this->title);
            $this->crudColumns = $module->getFillable();
        } else {
            $this->component = 'admin.' . Str::slug($this->title);
        }

        // Determine slug and use it for default icon.
        $this->slug = Str::slug($this->title);
        $this->icon = 'fa-solid fa-' . $this->slug;

        // Set or override other passed attributes.
        foreach ($attributes as $key => $value) {
            $this->$key = $value;
        }
    }

    private static function makeLabel(string $string): string
    {
        return ucfirst(str_replace('_', ' ', $string));
    }

    /**
     * Return the icon as html
     *
     * @return string
     */
    public function icon(): string
    {
        if (str_starts_with($this->icon, 'fa')) {
            return '<i class="icon ' . $this->icon . '"></i>';
        } elseif (str_starts_with($this->icon, '<')) {
            return $this->icon;
        } else {
            return '<img class="icon" src="' . $this->icon . '" alt="' . $this->title . '">';
        }
    }

    public function getCrudColumns()
    {
        $columns = [];
        foreach ($this->crudColumns as $column => $options) {
            if (is_string($options)) {
                $column = $options;
                $options = [];
            }
            $options['column'] = $column;
            $options['label'] = __($options['label'] ?? self::makeLabel($column));
            $options['type'] = null;
            $columns[] = (object)$options;
        }
        return $columns;
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
    public function offsetGet($offset): mixed
    {
        return $this->$offset ?? null;
    }
}

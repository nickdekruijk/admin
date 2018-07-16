<?php

namespace NickDeKruijk\Admin;

trait Images {

    public function images($column = 'images', $n = null)
    {
        if (empty(trim($this->$column))) {
            return null;
        }
        $images = array_map('trim', explode(chr(10), trim($this->$column)));
        return $n !== null ? explode('|', $images[$n], 2) : $images;
    }

    public function image($column = 'images', $n = 0)
    {
        return trim($this->images($column, $n)[0]);
    }

    public function imageCaption($column = 'images', $n = 0)
    {
        return trim($this->images($column, $n)[1]);
    }

}

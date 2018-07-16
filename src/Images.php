<?php

namespace NickDeKruijk\Admin;

trait Images {

    public function images($column = 'images', $index = null)
    {
        if (empty($this->$column) || !trim($this->$column)) {
            return null;
        }
        $images = explode(chr(10), trim($this->$column));
        return $index !== null ? explode('|', trim($images[$index]), 2) : $images;
    }

    public function image($column = 'images', $index = 0)
    {
        return trim($this->images($column, $index)[0]) ?: null;
    }

    public function imageCaption($column = 'images', $index = 0)
    {
        return trim($this->images($column, $index)[1] ?? null) ?: null;
    }

    public function imageCount($column = 'images')
    {
        return count($this->images($column));
    }

}

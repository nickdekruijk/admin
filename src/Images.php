<?php

namespace NickDeKruijk\Admin;

trait Images {

    public function images($column = 'images', $n = null)
    {
        if (empty($this->$column) || !trim($this->$column)) {
            return null;
        }
        $images = explode(chr(10), trim($this->$column));
        return $n !== null ? explode('|', trim($images[$n]), 2) : $images;
    }

    public function image($column = 'images', $n = 0)
    {
        return trim($this->images($column, $n)[0]) ?: null;
    }

    public function imageCaption($column = 'images', $n = 0)
    {
        return trim($this->images($column, $n)[1] ?? null) ?: null;
    }

    public function imageCount($column = 'images')
    {
        return count($this->images($column));
    }

}

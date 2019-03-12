<?php

namespace NickDeKruijk\Admin;

trait Images {

    public function images($column = null, $index = null)
    {
        return $this->imagesParse($column, $index, true);
    }

    public function imagesParse($column = null, $index = null, $array = false)
    {
        if (is_numeric($column)) {
            $index = $column;
            $column = null;
        }
        if ($column === null) {
            $column = $this->imagesColumn ?: 'images';
        }
        if (empty($this->$column) || !trim($this->$column)) {
            return null;
        }
        $images = explode(chr(10), trim($this->$column));
        if ($array) {
            $array = [];
            foreach($images as $image) {
                $image = explode('|', $image);
                $array[] = [
                    'file' => $image[0],
                    'caption' => $image[1] ?? null,
                ];
            }
            return $array;
        }
        return $index !== null ? explode('|', trim($images[$index]), 2) : $images;
    }

    public function image($column = null, $index = 0)
    {
        return trim($this->imagesParse($column, $index)[0]) ?: null;
    }

    public function imageCaption($column = null, $index = 0)
    {
        return trim($this->imagesParse($column, $index)[1] ?? null) ?: null;
    }

    public function imageCount($column = null)
    {
        return count($this->imagesParse($column));
    }

}

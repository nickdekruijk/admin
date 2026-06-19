<?php

namespace NickDeKruijk\Admin;

trait Images
{
    private static array $imagesParseCache = [];

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

        $cacheKey = spl_object_id($this) . '|' . $column . '|' . ($index === null ? 'N' : $index) . '|' . ($array ? 'A' : 'S');

        if (array_key_exists($cacheKey, self::$imagesParseCache)) {
            return self::$imagesParseCache[$cacheKey];
        }

        if (empty($this->$column) || !trim($this->$column)) {
            return self::$imagesParseCache[$cacheKey] = null;
        }

        $images = explode(chr(10), trim($this->$column));

        if ($array) {
            $result = [];
            foreach ($images as $image) {
                $image = explode('|', $image, 2);
                $result[] = [
                    'file' => trim($image[0]),
                    'caption' => trim($image[1] ?? ''),
                    'autocaption' => trim($image[1] ?? '') ?: pathinfo($image[0])['filename'],
                ];
            }
            return self::$imagesParseCache[$cacheKey] = $result;
        }

        $result = $index !== null ? explode('|', trim($images[$index] ?? null), 2) : $images;
        return self::$imagesParseCache[$cacheKey] = $result;
    }

    public function image($column = null, $index = 0)
    {
        return trim($this->imagesParse($column, $index)[0] ?? '') ?: null;
    }

    public function imageCaption($column = null, $index = 0)
    {
        return trim($this->imagesParse($column, $index)[1] ?? '') ?: null;
    }

    public function imageCount($column = null)
    {
        return count($this->imagesParse($column));
    }
}

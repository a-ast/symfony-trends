<?php


namespace Aa\ATrends\Util;

class ArrayUtils
{
    /**
     * Merges values to one array and removes empty items.
     *
     * @param mixed[] ...$values
     *
     * @return array
     */
    public static function trimMerge(...$values)
    {
        $result = [];
        foreach ($values as $value) {
            $array = (!is_array($value)) ? [$value] : $value;
            $result = array_merge($result, $array);
        }

        return array_filter($result);
    }

    /**
     * Trims array
     *
     * @param $array
     *
     * @return array
     */
    public static function trim($array)
    {
        return array_filter($array);
    }

    /**
     * Return first non empty item
     *
     * @param array $array
     * @param string $default
     *
     * @return mixed|string
     */
    public static function getFirstNonEmptyElement(array $array, $default = '')
    {
        foreach ($array as $item) {
            if ('' !== $item) {
                return $item;
            }
        }

        return $default;
    }
}

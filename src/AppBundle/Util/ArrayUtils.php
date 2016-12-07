<?php


namespace AppBundle\Util;

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
     * @param array $array
     * @param string|int $columnName
     *
     * @return array
     */
    public static function castArrayColumnToInt(&$array, $columnName)
    {
        return array_map(function ($value) {
            return (int)$value;
        }, array_column($array, $columnName));
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
}

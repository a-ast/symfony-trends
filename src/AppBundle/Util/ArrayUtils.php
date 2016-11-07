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
}

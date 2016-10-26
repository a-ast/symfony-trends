<?php


namespace AppBundle\Util;

class ArrayUtils
{
    public static function trimMerge($array1, $array2)
    {
        $a1 = (!is_array($array1)) ? [$array1] : $array1;
        $a2 = (!is_array($array2)) ? [$array2] : $array2;

        return array_filter(array_merge($a1, $a2));
    }
}

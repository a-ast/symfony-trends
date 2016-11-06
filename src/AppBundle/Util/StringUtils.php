<?php


namespace AppBundle\Util;

class StringUtils
{
    /**
     * @param string $text
     * @param string $substring
     *
     * @return string
     */
    public static function textAfter($text, $substring)
    {
        if (false !== ($pos = strpos($text, $substring))) {
            return substr($text, $pos + strlen($substring));
        }

        return '';
    }
}
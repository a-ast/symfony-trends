<?php


namespace Aa\ATrends\Util;

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

    /**
     * @param string $text
     * @param string $substring
     *
     * @return bool
     */
    public static function contains($text, $substring)
    {
        return (false !== strpos($text, $substring));
    }

    /**
     * @param string $text
     * @param string $substring
     *
     * @return bool
     */
    public static function startsWith($text, $substring)
    {
        return (0 === strpos($text, $substring));
    }
}

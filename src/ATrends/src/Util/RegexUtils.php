<?php

namespace Aa\ATrends\Util;

class RegexUtils
{
    /**
     * @param $text
     * @param array $regexes
     *
     * @return bool
     */
    public static function match($text, array $regexes)
    {
        foreach ($regexes as $regex) {
            $match = preg_match('/'.$regex.'/i', $text);
            if (false === $match) {
                throw new \UnexpectedValueException(sprintf('%s is not valid regular expression'));
            }

            if (0 !== $match) {
                return true;
            }
        }

        return false;
    }
}

<?php

namespace Lyric\Support;

class Strings
{
    /**
     * Transform string in pascal format to human format
     * StringExample => String Example
     *
     * @param string $string
     *
     * @return string
     */
    public static function pascalCaseToHuman($string)
    {
        return preg_replace("/([a-z])([A-Z])/", "$1 $2", $string);
    }

    /**
     * Transform string to slug format
     * String Example => string-example
     *
     * @param string $string
     *
     * @return string
     */
    public static function slug($string)
    {
        return strtolower(str_replace(' ', '-', self::pascalCaseToHuman($string)));
    }
}
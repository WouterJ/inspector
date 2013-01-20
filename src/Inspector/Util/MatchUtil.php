<?php

namespace Inspector\Util;

/**
 * All match related check- and parse functions.
 *
 * @author Wouter J <wouter@wouterj.nl>
 */
class MatchUtil
{
    /**
     * Checks if the current string is a regex.
     *
     * @param string $regex
     *
     * @return boolean
     */
    public static function isRegex($regex)
    {
        $start = substr($regex, 0, 1);
        $end = substr($regex, -1);

        return $start === $end || ('{' === $start && '}' === $end);
    }

    /**
     * Checks if the string is a glob (e.g. `*.php`)
     *
     * @param string $glob
     *
     * @return boolean
     */
    public static function isGlob($glob)
    {
        return 0 !== preg_match('/\*(?<!\\\)/', $glob);
    }

    /**
     * Checks if the string is a match (regex or glob).
     *
     * @param string $str
     *
     * @return boolean
     */
    public static function isMatch($str)
    {
        return self::isRegex($str) || self::isGlob($str);
    }

    /**
     * Converts a glob in a regex.
     *
     * @param string $glob
     *
     * @return string
     */
    public static function convertGlob($glob)
    {
            return '/'.str_replace(array('/', '.', '*'), array('\/', '\.', '.*?'), $glob).'/';
    }
}

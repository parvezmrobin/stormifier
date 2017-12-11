<?php
/**
 * User: Parvez
 * Date: 12/11/2017
 * Time: 4:42 AM
 */

if (!function_exists('container')) {
    /**
     * @return \DI\Container
     */
    function container()
    {
        return \Stormifier\Base\Storm::getContainer();
    }
}

if (!function_exists('storm')) {
    /**
     * @return \Stormifier\Base\Storm
     */
    function storm()
    {
        return \Stormifier\Base\Storm::getStorm();
    }
}

if (!function_exists('endsWith')) {
    /**
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    function endsWith(string $haystack, string $needle)
    {
        $stringLen = strlen($haystack);
        $testLen = strlen($needle);
        if ($testLen > $stringLen) return false;
        return substr_compare($haystack, $needle, $stringLen - $testLen, $testLen, true) === 0;
    }
}
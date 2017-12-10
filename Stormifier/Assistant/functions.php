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
    function container() {
        return \Stormifier\Base\Storm::getContainer();
    }
}

if (!function_exists('storm')) {
    /**
     * @return \Stormifier\Base\Storm
     */
    function storm() {
        return \Stormifier\Base\Storm::getStorm();
    }
}
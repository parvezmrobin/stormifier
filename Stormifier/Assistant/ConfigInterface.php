<?php
/**
 * User: Parvez
 * Date: 10/15/2017
 * Time: 1:22 AM
 */

namespace Stormifier\Assistant;


interface ConfigInterface
{
    /**
     * @param $basePath
     * @param $fileName
     * @return ConfigInterface
     */
    public static function from($basePath, $fileName);

    /**
     * @param string $key
     * @return string
     */
    public function get($key);
}
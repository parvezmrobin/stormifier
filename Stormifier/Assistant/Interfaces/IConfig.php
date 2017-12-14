<?php
/**
 * User: Parvez
 * Date: 10/15/2017
 * Time: 1:22 AM
 */

namespace Stormifier\Assistant\Interfaces;


interface IConfig
{
    /**
     * @param string $basePath
     * @param string $fileName
     * @return IConfig
     */
    public static function from(string $fileName, string $basePath): IConfig;

    /**
     * @param string $key
     * @return string
     */
    public function get($key): string;

    /**
     * @return array
     */
    public function all(): array;
}
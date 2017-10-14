<?php
/**
 * User: Parvez
 * Date: 10/15/2017
 * Time: 1:20 AM
 */

namespace Stormifier\Assistant;


use Symfony\Component\Yaml\Yaml;

class Config implements ConfigInterface
{
    protected $data;

    /**
     * Config constructor.
     * @param string $basePath
     * @param string $filename
     */
    function __construct($basePath, $filename)
    {
        if (!ends_with($filename, ".yaml")) {
            $filename .= ".yaml";
        }
        $this->data = Yaml::parse(file_get_contents($basePath . "/config/" . $filename));
    }

    /**
     * @param $basePath
     * @param $fileName
     * @return Config
     */
    public static function from($basePath, $fileName)
    {
        return new static($basePath, $fileName);
    }

    /**
     * @param string $key
     * @return string
     */
    public function get($key)
    {
        return $this->data[$key];
    }
}
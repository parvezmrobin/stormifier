<?php
/**
 * User: Parvez
 * Date: 10/15/2017
 * Time: 1:20 AM
 */

namespace Stormifier\Assistant;


use Stormifier\Assistant\Interfaces\IConfig;

class Config implements IConfig
{
    /**
     * @var array
     */
    protected $data;

    /**
     * Config constructor.
     * @param string $basePath
     * @param string $filename
     */
    function __construct(string $filename, string $basePath = null)
    {
        if (is_null($basePath)) {
            $basePath = $this->storm->getBasePath();
        }

        if (\endsWith($filename, ".php")) {
            $filename .= ".php";
        }
        $this->data = require($basePath . "/config/" . $filename);
    }

    /**
     * @inheritdoc
     */
    public static function from(string $fileName, string $basePath = null)
    {
        return new static($fileName, $basePath);
    }

    /**
     * @inheritdoc
     */
    public function get($key)
    {
        return $this->data[$key];
    }

    public function all()
    {
        return $this->data;
    }
}
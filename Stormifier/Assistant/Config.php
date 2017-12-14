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
    protected static $loads = [];
    /**
     * @var array
     */
    protected $data;

    /**
     * Config constructor.
     * @param string $basePath
     * @param string $fileName
     */
    function __construct(string $fileName, string $basePath = null)
    {
        if (is_null($basePath)) {
            $basePath = \storm()->getBasePath();
        }

        if (\endsWith($fileName, ".php")) {
            $fileName .= ".php";
        }
        $this->data = require($basePath . "/config/" . $fileName);
        static::$loads[$fileName] = $this;
    }

    /**
     * @inheritdoc
     */
    public static function from(string $fileName, string $basePath = null): IConfig
    {
        if (isset(static::$loads[$fileName])) {
            return static::$loads[$fileName];
        }
        return new static($fileName, $basePath);
    }

    /**
     * @inheritdoc
     */
    public function get($key): string
    {
        return $this->data[$key];
    }

    /**
     * @inheritdoc
     */
    public function all(): array
    {
        return $this->data;
    }
}
<?php
/**
 * User: Parvez
 * Date: 10/15/2017
 * Time: 1:20 AM
 */

namespace Stormifier\Assistant;


use Stormifier\Assistant\Interfaces\IConfig;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

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

        // If $fileName doesn't have .php extension, add it
        if (\endsWith($fileName, ".php")) {
            $fileName .= ".php";
        }

        // Read the config file, if exists
        if (file_exists($filePath = $basePath . "/config/" . $fileName)) {
            $this->data = require($filePath);
            static::$loads[$fileName] = $this;
        } else {
            throw new FileNotFoundException($filePath . " not found");
        }
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
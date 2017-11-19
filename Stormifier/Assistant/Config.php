<?php
/**
 * User: Parvez
 * Date: 10/15/2017
 * Time: 1:20 AM
 */

namespace Stormifier\Assistant;


use Symfony\Component\Yaml\Yaml;
use Stormifier\Assistant\Interfaces\IConfig;

class Config implements IConfig
{
    protected $data;

    /**
     * Config constructor.
     * @param string $basePath
     * @param string $filename
     */
    function __construct(string $filename, string $basePath = null)
    {
        if (is_null($basePath)) {
            $basePath = $GLOBALS['app']->getBasePath();
        }
        if (!$this->endsWith($filename, ".yaml")) {
            $filename .= ".yaml";
        }
        $this->data = Yaml::parse(file_get_contents($basePath . "/config/" . $filename));
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

    /**
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    private function endsWith(string $haystack, string $needle) {
        $stringLen = strlen($haystack);
        $testLen = strlen($needle);
        if ($testLen > $stringLen) return false;
        return substr_compare($haystack, $needle, $stringLen - $testLen, $testLen, true) === 0;
    }
}
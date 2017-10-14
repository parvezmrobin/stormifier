<?php
/**
 * User: Parvez
 * Date: 10/15/2017
 * Time: 1:47 AM
 */

namespace Stormifier\Assistant;


use Mustache_Engine;
use Mustache_Loader_FilesystemLoader;
use Symfony\Component\HttpFoundation\Response;

class View
{
    protected $view;
    protected $data;

    /**
     * View constructor.
     * @param string $viewName
     * @param array $data
     */
    function __construct($viewName, $data = []) {
        $this->view = $viewName;
        $this->data = $data;
    }

    /**
     * @param string $viewName
     * @param array $data
     * @return static
     */
    public static function file($viewName, $data = [])
    {
        return new static($viewName, $data);
    }

    /**
     * @param string $key
     * @param string $value
     * @return $this
     */
    public function with($key, $value)
    {
        $this->data[$key] = $value;
        return $this;
    }

    public function render()
    {
        $mustache = new Mustache_Engine(array(
            'loader' => new Mustache_Loader_FilesystemLoader(
                $GLOBALS['app']->getBasePath() . "\\resources\\views",
                array('extension' => '.html')
            ),
            'partials_loader' => new Mustache_Loader_FilesystemLoader(
                $GLOBALS['app']->getBasePath() . "/resources/views/partials"
            ),
            'strict_callables' => true,
        ));

        $template = $mustache->render($this->view, $this->data);
        return Response::create($template);
    }
}
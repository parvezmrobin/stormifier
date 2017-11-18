<?php
/**
 * User: Parvez
 * Date: 10/15/2017
 * Time: 1:47 AM
 */

namespace Stormifier\Assistant;


use Symfony\Component\HttpFoundation\Response;
use Twig_Environment;
use Twig_Loader_Filesystem;

class View
{
    protected $view;
    protected $data;

    /**
     * View constructor.
     * @param string $viewName
     * @param array $data
     */
    function __construct($viewName, $data = [])
    {
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
        $loader = new Twig_Loader_Filesystem($GLOBALS['app']->getBasePath() . "\\resources\\views");
        $twig = new Twig_Environment($loader, array(
            'cache' => $GLOBALS['app']->getBasePath() . "\\resources\\views\\Cache",
        ));
        $template = $twig->load($this->view . '.twig');

//        $mustache = new Mustache_Engine(array(
//            'loader' => new Mustache_Loader_FilesystemLoader(
//                $GLOBALS['app']->getBasePath() . "\\resources\\views",
//                array('extension' => '.html')
//            ),
//            'partials_loader' => new Mustache_Loader_FilesystemLoader(
//                $GLOBALS['app']->getBasePath() . "/resources/views/partials"
//            ),
//            'strict_callables' => true,
//        ));
//
//        $template = $mustache->render($this->view, $this->data);
        return Response::create($template->render($this->data));
    }
}
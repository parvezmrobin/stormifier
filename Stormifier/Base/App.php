<?php
/**
 * User: Parvez
 * Date: 10/9/2017
 * Time: 8:02 PM
 */

namespace Stormifier\Base;


use Stormifier\Assistant\Config;
use Symfony\Component\Debug\Debug;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Yaml\Yaml;

class App
{
    protected $basePath;
    protected $request;
    protected $dispatcher;
    protected $ctrlResolver;
    protected $argResolver;
    protected $matcher;
    private $kernel;

    /**
     * App constructor.
     * @param $basePath
     */
    function __construct($basePath)
    {
        $this->basePath = $basePath;
        $this->request = Request::createFromGlobals();
        $routeCollection = $this->makeRouteCollection();
        $this->matcher = new UrlMatcher(
            $routeCollection, new RequestContext()
        );
        $this->dispatcher = new EventDispatcher();
        $this->dispatcher->addSubscriber(new RouterListener(
            $this->matcher, new RequestStack()
        ));
        $this->ctrlResolver = new ControllerResolver();
        $this->argResolver = new ArgumentResolver();
        $this->kernel = new HttpKernel(
            $this->dispatcher,
            $this->ctrlResolver,
            new RequestStack(),
            $this->argResolver
        );

        $this->init();
    }

    /**
     * @return RouteCollection
     */
    private function makeRouteCollection()
    {
        $routesArray = Yaml::parse(file_get_contents($this->basePath . "/config/routs.yaml"));
        $collection = new RouteCollection();

        foreach ($routesArray as $name => $route) {
            $collection->add($name, new Route($route['path'], $route['defaults']));
        }

        return $collection;
    }

    private function init()
    {
        $this->startDebug();
        $GLOBALS['app'] = $this;
        $response = $this->kernel->handle($this->request);
        $response->send();

        $this->kernel->terminate($this->request, $response);
    }

    private function startDebug()
    {
        $isDev = Config::from($this->basePath, "env")->get('dev');
        if ($isDev) Debug::enable();
    }

    /**
     * @return string
     */
    public function getBasePath()
    {
        return $this->basePath;
    }
}
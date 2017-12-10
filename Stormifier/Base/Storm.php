<?php
/**
 * User: Parvez
 * Date: 10/9/2017
 * Time: 8:02 PM
 */

namespace Stormifier\Base;


use DI\ContainerBuilder;
use DI\Container;
use Stormifier\Assistant\Config;
use Symfony\Component\Debug\Debug;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class Storm
{
    /**
     * @var Container
     */
    protected static $container;
    /**
     * @var Storm
     */
    private static $storm;
    protected $basePath;
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
        $routeCollection = $this->makeRouteCollection();
        $this->matcher = new UrlMatcher(
            $routeCollection, new RequestContext()
        );
        $this->dispatcher = new EventDispatcher();
        $this->dispatcher->addSubscriber(
            new RouterListener(
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

        static::$container = ContainerBuilder::buildDevContainer();
        static::$container->set('storm', $this);
        static::$storm = $this;

        $this->init();
    }

    /**
     * @return RouteCollection
     */
    private function makeRouteCollection()
    {
        $routesArray = require($this->basePath . "/config/routs.php");
        $collection = new RouteCollection();

        foreach ($routesArray as $name => $route) {
            $collection->add($name, new Route($route['path'], $route['defaults']));
        }

        return $collection;
    }

    /**
     * Initializes the system
     */
    private function init()
    {
        $this->startDebug();
    }

    /**
     * Starts debugging in development mode
     */
    private function startDebug()
    {
        $isDev = Config::from("env", $this->basePath)->get('dev');
        if ($isDev) Debug::enable();
    }

    /**
     * @return Container
     */
    public static function getContainer(): Container
    {
        return static::$container;
    }

    /**
     * @return string
     */
    public function getBasePath(): string
    {
        return $this->basePath;
    }

    /**
     * @return HttpKernel
     */
    public function getKernel():  HttpKernel
    {
        return $this->kernel;
    }

    /**
     * @return Storm
     */
    public static function getStorm()
    {
        return static::$storm;
    }
}
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
     * @param string|null $kernel
     */
    function __construct($basePath, $kernel = null)
    {

        $this->setupContainer();

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

        if (is_null($kernel)) {
            $this->kernel = static::$container->make(
                HttpKernel::class,
                [
                    "dispatcher" => $this->dispatcher,
                ]);
        } else {
            $this->kernel = static::$container->make($kernel, array_slice(func_get_args(), 2, null, true));
        }

        $this->init();
    }

    protected function setupContainer()
    {
        static::$container = ContainerBuilder::buildDevContainer();
        static::$container->set('storm', $this);
        static::$storm = $this;
    }

    /**
     * @return RouteCollection
     */
    private function makeRouteCollection()
    {
        $routesArray = require($this->basePath . "/config/routes.php");
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
     * @return Storm
     */
    public static function getStorm()
    {
        return static::$storm;
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
    public function getKernel(): HttpKernel
    {
        return $this->kernel;
    }
}
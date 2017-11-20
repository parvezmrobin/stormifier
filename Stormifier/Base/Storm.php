<?php
/**
 * User: Parvez
 * Date: 10/9/2017
 * Time: 8:02 PM
 */

namespace Stormifier\Base;


use DI\ContainerBuilder;
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
use Symfony\Component\Yaml\Yaml;

class Storm
{
    protected $basePath;
    protected $dispatcher;
    protected $ctrlResolver;
    protected $argResolver;
    protected $matcher;
    private $kernel;

    /**
     * @var \Psr\Container\ContainerInterface
     */
    public $container;

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

        $this->container = ContainerBuilder::buildDevContainer();
        $this->container->set('storm', $this);

        $GLOBALS['storm'] = $this;
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

    private function init()
    {
        $this->startDebug();
    }

    private function startDebug()
    {
        $isDev = Config::from("env", $this->basePath)->get('dev');
        if ($isDev) Debug::enable();
    }

    /**
     * @return string
     */
    public function getBasePath()
    {
        return $this->basePath;
    }

    /**
     * @return HttpKernel
     */
    public function getKernel()
    {
        return $this->kernel;
    }
}
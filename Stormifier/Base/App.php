<?php
/**
 * User: Parvez
 * Date: 10/9/2017
 * Time: 8:02 PM
 */

namespace Stormifier\Base;


use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\HttpKernel;

class App
{
    protected $basePath;
    protected $request;
    protected $dispatcher;
    protected $ctrlResolver;
    protected $argResolver;
    private $kernel;

    /**
     * App constructor.
     * @param $basePath
     */
    function __construct($basePath)
    {
        $this->basePath = $basePath;
        $this->request = Request::createFromGlobals();
        $this->dispatcher = new EventDispatcher();
        $this->ctrlResolver = new ControllerResolver();
        $this->argResolver = new ArgumentResolver();
        $this->kernel = new HttpKernel(
            $this->dispatcher, $this->ctrlResolver, new RequestStack(), $this->argResolver
        );
        $this->init();
    }

    private function init()
    {
        $response = $this->kernel->handle($this->request);
        $response->send();

        $this->kernel->terminate($this->request, $response);
    }
}
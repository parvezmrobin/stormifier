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

class View extends StormResponse
{
    protected $view;

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
     * @inheritdoc
     * @internal param string $view
     */
    public static function response(array $data): Response
    {
        if (func_num_args() === 1) {
            throw new \InvalidArgumentException("Parameter \$view not given");
        }
        return (new static(func_get_arg(1), $data))->respond();
    }

    /**
     * @inheritdoc
     */
    public function respond(): Response
    {
        $storm = storm();
        $loader = new Twig_Loader_Filesystem($storm->getBasePath() . "\\resources\\views");
        $twig = new Twig_Environment(
            $loader, array(
            'cache' => $storm->getBasePath() . "\\resources\\views\\Cache",
            'debug' => Config::from('env')->get('dev'),
            'strict_variables' => true
        ));
        $template = $twig->load($this->view . '.twig');

        return Response::create($template->render($this->data));
    }
}
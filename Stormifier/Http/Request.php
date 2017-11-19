<?php
/**
 * User: Parvez
 * Date: 11/19/2017
 * Time: 9:20 PM
 */

namespace Stormifier\Http;


use Stormifier\Base\Storm;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class Request extends SymfonyRequest
{
    /**
     * @var Storm
     */
    public $storm;

    /**
     * @var ParameterBag[]
     */
    public $parameterBags;

    public function __construct(
        array $query = array(),
        array $request = array(),
        array $attributes = array(),
        array $cookies = array(),
        array $files = array(),
        array $server = array(),
        $content = null
    )
    {
        $this->storm = $GLOBALS['storm'];

        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);

        $this->parameterBags = [
            $this->query,
            $this->request,
            $this->attributes,
            $this->cookies,
            $this->server,
            $this->headers
        ];
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        foreach ($this->parameterBags as $parameterBag) {
            if ($parameterBag->has($key)) {
                return true;
            }
        }

        return false;
    }
}
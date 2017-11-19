<?php
/**
 * User: Parvez
 * Date: 11/19/2017
 * Time: 9:20 PM
 */

namespace Stormifier\Http;


use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class Request extends SymfonyRequest
{
    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        $parameterBags = [
            $this->query,
            $this->request,
            $this->attributes,
            $this->cookies,
            $this->server,
            $this->headers
        ];

        foreach ($parameterBags as $parameterBag) {
            if ($parameterBag->has($key)) {
                return true;
            }
        }

        return false;
    }
}
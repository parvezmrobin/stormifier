<?php
/**
 * User: Parvez
 * Date: 12/11/2017
 * Time: 4:56 AM
 */

namespace Stormifier\Assistant;


use Symfony\Component\HttpFoundation\Response;

abstract class StormResponse
{
    protected $data;

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

    /**
     * Respond to a request
     * @return Response
     */
    public abstract function respond(): Response;

    public abstract static function response(array $data): Response;
}
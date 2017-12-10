<?php
/**
 * User: Parvez
 * Date: 10/15/2017
 * Time: 3:07 AM
 */

namespace Stormifier\Assistant;


use Symfony\Component\HttpFoundation\Response;

class Json extends StormResponse
{
    /**
     * Json constructor.
     * @param array $data
     */
    function __construct($data = []) {
        $this->data = $data;
    }

    /**
     * @param array $data
     * @return static
     */
    public static function json($data = [])
    {
        return new static($data);
    }

    /**
     * @param array $data
     * @return Response
     */
    public static function response(array $data): Response
    {
        return new Response(json_encode($data));
    }


    /**
     * @inheritdoc
     */
    public function respond(): Response
    {
        return new Response(json_encode($this->data));
    }
}
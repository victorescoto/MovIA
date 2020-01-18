<?php

namespace App\Http;

use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;

class Request extends HttpFoundationRequest
{
    /**
     * Request json parameters.
     *
     * @var ParameterBag
     */
    public $json;

    public function __construct(array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], $content = null)
    {
        $this->initialize($query, $request, $attributes, $cookies, $files, $server, $content);

        $jsonContent = json_decode($this->getContent(), true) ?? [];

        $this->json = new ParameterBag($jsonContent);
    }
}

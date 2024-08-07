<?php

namespace App\Utils;

use Psr\Http\Message\ResponseInterface;

class Http
{
    protected $client;

    public function __construct($config = [])
    {
        $this->client = new \GuzzleHttp\Client($config);
    }

    public function get(String $path = '', $options = []): ResponseInterface
    {
        return $this->client->get($path, $options);
    }
}

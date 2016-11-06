<?php

namespace AppBundle\Client;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Kevinrob\GuzzleCache\CacheMiddleware;

class HttpClient extends Client
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $stack = HandlerStack::create();
        $stack->push(new CacheMiddleware(), 'cache');

        parent::__construct(['handler' => $stack]);
    }
}

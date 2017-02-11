<?php

namespace features\Aa\ATrends\Fake;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class FakeHttpClient extends Client
{

    /**
     * Constructor.
     */
    public function __construct()
    {
        $mock = new MockHandler([
            new Response(403, ['Date' => 'Wed, 21 Oct 2015 04:29:00 GMT', 'X-RateLimit-Reset' => 1445401800]),
            new Response(200, []),
        ]);

        $handler = HandlerStack::create($mock);

        parent::__construct(['handler' => $handler, 'http_errors' => false]);
    }
}

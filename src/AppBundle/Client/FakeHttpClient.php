<?php


namespace AppBundle\Client;

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
            new Response(403, ['Date' => 'Sat, 31 Dec 2016 18:53:02 GMT','X-RateLimit-Reset' => 1483211005]),
        ]);

        $handler = HandlerStack::create($mock);

        parent::__construct(['handler' => $handler]);
    }
}

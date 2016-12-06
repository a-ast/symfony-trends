<?php


namespace Tests\AppBundle\Traits;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

trait HttpClientAwareTrait
{
    /**
     * @param array $responseData
     *
     * @return ClientInterface
     */
    public function getHttpClient(array $responseData)
    {
        $responses = [];
        foreach ($responseData as $item) {
            $responses[] = new Response($item[0], $item[1], $item[2]);
        }

        $mock = new MockHandler($responses);

        $handler = HandlerStack::create($mock);
        return new Client(['handler' => $handler]);
    }
}

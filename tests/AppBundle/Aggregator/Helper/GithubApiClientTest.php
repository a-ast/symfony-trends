<?php


namespace tests\AppBundle\Aggregator\Helper;


use AppBundle\Aggregator\Helper\GithubApiClient;
use PHPUnit_Framework_TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;

class GithubApiClientTest extends PHPUnit_Framework_TestCase
{
    public function testAuthorize()
    {
        $mock = new MockHandler([
            new Response(200, [], '{ "token": "token" }'),
        ]);

        $handler = HandlerStack::create($mock);
        $httpClient = new Client(['handler' => $handler]);

        $apiClient = new GithubApiClient($httpClient, 'client_id', 'client_secret');

        $apiClient->authenticate();
    }

    public function testFindUser()
    {
        $mock = new MockHandler([
            //new Response(200, [], '{ "token": "token" }'),
            new Response(200, ['X-RateLimit-Remaining' => '10'], '{ "total_count": 1 }'),
        ]);

        $handler = HandlerStack::create($mock);
        $httpClient = new Client(['handler' => $handler]);

        $apiClient = new GithubApiClient($httpClient, 'client_id', 'client_secret');
        $searchResults = $apiClient->findUser('Legolas', 'user');

        $this->assertEquals(1, $searchResults['total_count']);
        $this->assertEquals(10, $searchResults['request_limit']);
    }
}

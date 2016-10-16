<?php


namespace Tests\AppBundle;

use AppBundle\ContributorsCrawler;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit_Framework_TestCase;

class ContributorsDataCrawlerTest extends PHPUnit_Framework_TestCase
{
    public function testUpdateContributors()
    {
        $responseBody = 'CONTRIBUTORS
============

Symfony is the result of the work of many people who made the code better
(see https://symfony.com/contributors for more information):

 - Fabien Potencier (fabpot)
 - Nicolas Grekas (nicolas-grekas)
 - Bernhard Schussek (bschussek)';
        
        
        $mock = new MockHandler([
            new Response(200, [], $responseBody),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $crawler = new ContributorsCrawler($client);

        $this->assertEquals(3, $crawler->getData('any'));
    }
}

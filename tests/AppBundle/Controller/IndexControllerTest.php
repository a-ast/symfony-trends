<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class IndexControllerTest extends WebTestCase
{
    public function testIndexPage()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/trends/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertEquals(1, $crawler->filter('h1')->count());
        $this->assertEquals('Symfony Trends beta', $crawler->filter('h1')->text());

        $chartCount = $crawler->filter('div.chart')->count();
        $h2Count = $crawler->filter('h2')->count();

        $this->assertGreaterThan(0, $chartCount);
        $this->assertEquals($chartCount, $h2Count);
    }
}

<?php

namespace AppBundle\Client\PageCrawler;

use Http\Client\HttpClient;
use Http\Discovery\MessageFactoryDiscovery;
use Symfony\Component\DomCrawler\Crawler;

class PageCrawler implements PageCrawlerInterface
{
    /**
     * @var HttpClient
     */
    private $client;

    public function __construct(HttpClient $client)
    {
        $this->client = $client;
    }

    /**
     * @inheritdoc
     */
    public function getDomCrawler($uri)
    {
        $messageFactory = MessageFactoryDiscovery::find();

        $request = $messageFactory->createRequest('GET', $uri);
        $response = $this->client->sendRequest($request);

        $crawler = new Crawler($response->getBody()->getContents(), $uri);

        return $crawler;
    }
}

<?php

namespace AppBundle\Client\PageCrawler;

use Symfony\Component\DomCrawler\Crawler;

interface PageCrawlerInterface
{
    /**
     * @param string $uri
     *
     * @return Crawler
     */
    public function getDomCrawler($uri);
}

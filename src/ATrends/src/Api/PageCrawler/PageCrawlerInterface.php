<?php

namespace Aa\ATrends\Api\PageCrawler;

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

<?php

namespace AppBundle\Aggregator\Helper;

use Symfony\Component\DomCrawler\Crawler;

interface CrawlerExtractorInterface
{
    /**
     * @param Crawler $crawler
     *
     * @return mixed
     */
    public function extract(Crawler $crawler);
}

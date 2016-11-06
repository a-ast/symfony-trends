<?php

namespace AppBundle\Client;

use Symfony\Component\DomCrawler\Crawler;

interface PageGetterInterface
{
    /**
     * @param string $url
     *
     * @return Crawler
     */
    public function getPageDom($url);
}

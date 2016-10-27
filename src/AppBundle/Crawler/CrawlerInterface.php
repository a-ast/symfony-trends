<?php

namespace AppBundle\Crawler;

interface CrawlerInterface
{
    public function getData(array $options = []);
}

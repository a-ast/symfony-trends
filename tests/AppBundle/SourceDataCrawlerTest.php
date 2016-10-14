<?php


namespace Tests\AppBundle;


use AppBundle\SourceDataCrawler;
use PHPUnit_Framework_TestCase;

class SourceDataCrawlerTest extends PHPUnit_Framework_TestCase
{
    public function testUpdateContributors()
    {
        $crawler = new SourceDataCrawler();

        $crawler->updateContributors();
    }
}

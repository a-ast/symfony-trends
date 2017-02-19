<?php

namespace AppBundle\Api\Sensiolabs;

use Aa\ATrends\Api\CrawlerExtractorInterface;
use Aa\ATrends\Api\PageCrawler\PageCrawlerInterface;
use AppBundle\Model\SensiolabsUser;

class SensiolabsApi
{
    /**
     * @var PageCrawlerInterface
     */
    private $pageCrawler;

    /**
     * @var CrawlerExtractorInterface
     */
    private $crawlerExtractor;

    public function __construct(PageCrawlerInterface $pageCrawler, CrawlerExtractorInterface $crawlerExtractor)
    {
        $this->pageCrawler = $pageCrawler;
        $this->crawlerExtractor = $crawlerExtractor;
    }

    /**
     * @param string $login
     *
     * @return SensiolabsUser
     */
    public function getUser($login)
    {
        return new SensiolabsUser('', '', '','', '', '','', '', '', '', '');
    }
}

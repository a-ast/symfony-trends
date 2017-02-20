<?php

namespace AppBundle\Api\Sensiolabs;

use Aa\ATrends\Api\CrawlerExtractorInterface;
use Aa\ATrends\Api\PageCrawler\PageCrawlerInterface;

class SensiolabsApi implements SensiolabsApiInterface
{
    const PROFILE_URL_FORMAT = 'https://connect.sensiolabs.com/profile/%s';

    /**
     * @var PageCrawlerInterface
     */
    private $pageCrawler;

    /**
     * @var CrawlerExtractorInterface
     */
    private $extractor;

    public function __construct(PageCrawlerInterface $pageCrawler, CrawlerExtractorInterface $extractor)
    {
        $this->pageCrawler = $pageCrawler;
        $this->extractor = $extractor;
    }

    /**
     * @inheritdoc
     */
    public function getUser($login)
    {
        $crawler = $this->pageCrawler->getDomCrawler(sprintf(self::PROFILE_URL_FORMAT, $login));

        return $this->extractor->extract($crawler);
    }
}

<?php

namespace AppBundle\Api\ContributorPage;

use Aa\ATrends\Api\CrawlerExtractorInterface;
use Aa\ATrends\Api\PageCrawler\PageCrawlerInterface;
use Aa\ATrends\Util\StringUtils;

class ContributorPageApi implements ContributorPageApiInterface
{

    /**
     * @var CrawlerExtractorInterface
     */
    private $extractor;

    /**
     * @var PageCrawlerInterface
     */
    private $pageCrawler;

    public function __construct(CrawlerExtractorInterface $extractor, PageCrawlerInterface $pageCrawler)
    {
        $this->extractor = $extractor;
        $this->pageCrawler = $pageCrawler;
    }

    /**
     * @inheritdoc
     */
    function getContributorLogins($uri, $profileUri)
    {
        $links = $this->getContributorLinks($uri);

        return $this->getContributorLoginsFromLinks($links, $profileUri);
    }

    private function getContributorLinks($uri)
    {
        $crawler = $this->pageCrawler->getDomCrawler($uri);

        $links = $this->extractor->extract($crawler);

        return $links;
    }

    private function getContributorLoginsFromLinks($links, $profileUri)
    {
        return array_map(function ($item) use ($profileUri) {
            return StringUtils::textAfter($item, $profileUri);
        }, $links);
    }
}

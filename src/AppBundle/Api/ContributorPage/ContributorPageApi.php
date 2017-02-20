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

    /**
     * Constructor.
     *
     * @param PageCrawlerInterface $pageCrawler
     * @param CrawlerExtractorInterface $extractor
     */
    public function __construct(PageCrawlerInterface $pageCrawler, CrawlerExtractorInterface $extractor)
    {
        $this->pageCrawler = $pageCrawler;
        $this->extractor = $extractor;
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

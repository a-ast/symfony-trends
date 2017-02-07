<?php

namespace AppBundle\Aggregator;

use AppBundle\Aggregator\Helper\CrawlerExtractorInterface;
use AppBundle\Client\PageCrawler\PageCrawlerInterface;
use AppBundle\Entity\Project;
use AppBundle\Helper\ProgressInterface;
use AppBundle\Repository\SensiolabsUserRepository;
use AppBundle\Util\StringUtils;

class ContributorPageAggregator implements AggregatorInterface
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
     * @var SensiolabsUserRepository
     */
    private $repository;

    /**
     * @var string
     */
    private $profileUri;

    /**
     * Constructor.
     *
     * @param PageCrawlerInterface $pageCrawler
     * @param CrawlerExtractorInterface $extractor
     * @param SensiolabsUserRepository $repository
     * @param string $profileUri
     */
    public function __construct(
        PageCrawlerInterface $pageCrawler,
        CrawlerExtractorInterface $extractor,
        SensiolabsUserRepository $repository,
        $profileUri
    ) {
        $this->pageCrawler = $pageCrawler;
        $this->extractor = $extractor;
        $this->repository = $repository;
        $this->profileUri = $profileUri;
    }

    /**
     * @inheritdoc
     */
    public function aggregate(Project $project, array $options, ProgressInterface $progress = null)
    {
        // @todo: get link from db
        $url = 'http://symfony.com/contributors/code';

        $links = $this->getContributorLinks($url);

        // @todo: iterate links, extract login, save to db

        return null;
    }

    private function getContributorLinks($uri)
    {
        $crawler = $this->pageCrawler->getDomCrawler($uri);

        $links = $this->extractor->extract($crawler);

        return $links;
    }

    /**
     * @param string $uri
     *
     * @return string
     */
    private function getSensiolabsLoginFromUri($uri)
    {
        return StringUtils::textAfter($uri, $this->profileUri);
    }

}

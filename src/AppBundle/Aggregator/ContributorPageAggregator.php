<?php

namespace AppBundle\Aggregator;

use AppBundle\Aggregator\Helper\ContributorExtractor;
use AppBundle\Entity\Project;
use AppBundle\Helper\ProgressInterface;
use AppBundle\Repository\ContributorRepository;
use AppBundle\Util\StringUtils;
use GuzzleHttp\ClientInterface;

class ContributorPageAggregator implements AggregatorInterface
{
    /**
     * @var ClientInterface
     */
    private $httpClient;
    /**
     * @var ContributorRepository
     */
    private $repository;
    /**
     * @var ContributorExtractor
     */
    private $extractor;

    /**
     * Constructor.
     *
     * @param ClientInterface $httpClient
     * @param ContributorExtractor $extractor
     * @param ContributorRepository $repository
     */
    public function __construct(
        ClientInterface $httpClient,
        ContributorExtractor $extractor,
        ContributorRepository $repository
    ) {
        $this->httpClient = $httpClient;
        $this->repository = $repository;
        $this->extractor = $extractor;
    }

    /**
     * @inheritdoc
     */
    public function aggregate(Project $project, array $options, ProgressInterface $progress = null)
    {
        // @todo: get link from db
        $url = $options['url'];

        $links = $this->getContributorLinks($url);

        // @todo: iterate links, request pages or/and store sensiolabs user

        return null;
    }

    protected function getContributorLinks($url)
    {
        $responseBody = $this->getPageContents($url);

        $links = $this->extractor->extract($responseBody);

        return $links;
    }

    /**
     * @param $uri
     * @return string
     */
    protected function getPageContents($uri)
    {
        $response = $this->httpClient->request('GET', $uri);

        $responseBody = $response->getBody();

        return (string)$responseBody;
    }


    private function getSensiolabsLoginFromUrl($url)
    {
        return StringUtils::textAfter($url, 'https://connect.sensiolabs.com/profile/');
    }

}

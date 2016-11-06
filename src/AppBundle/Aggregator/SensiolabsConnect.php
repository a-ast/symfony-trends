<?php

namespace AppBundle\Aggregator;

use AppBundle\Aggregator\Helper\SensiolabsDataExtractor;
use AppBundle\Client\PageGetterInterface;
use AppBundle\Helper\ProgressInterface;
use AppBundle\Repository\ContributorRepository;
use GuzzleHttp\ClientInterface;
use Symfony\Component\DomCrawler\Crawler;

class SensiolabsConnect implements AggregatorInterface
{
    /**
     * @var PageGetterInterface
     */
    private $httpClient;
    /**
     * @var ContributorRepository
     */
    private $repository;
    /**
     * @var SensiolabsDataExtractor
     */
    private $extractor;

    /**
     * Constructor.
     *
     * @param PageGetterInterface $httpClient
     * @param SensiolabsDataExtractor $extractor
     * @param ContributorRepository $repository
     */
    public function __construct(PageGetterInterface $httpClient,
        SensiolabsDataExtractor $extractor,
        ContributorRepository $repository)
    {
        $this->httpClient = $httpClient;
        $this->repository = $repository;
        $this->extractor = $extractor;
    }

    public function aggregate(array $options, ProgressInterface $progress = null)
    {
        $report = [];

        $contributors = $this->repository->findWithSensiolabsLogin();

        $progress->start(count($contributors));

        foreach ($contributors as $contributor) {
            $progress->advance();

            $login = $contributor->getSensiolabsLogin();
            $url = $this->getProfileUrl($login);

            $crawler = $this->httpClient->getPageDom($url);
            $data = $this->extractor->extract($crawler);

            if('' != $contributor->getGithubLogin() &&
                $data['github_login'] !== $contributor->getGithubLogin()
            ) {
                $report['unmatchedGuthubLogins'][] = $data['github_login'];

                continue;
            }

            $contributor
                ->setGithubLogin($data['github_login'])
                ->setSensiolabsCity($data['city'])
                ->setSensiolabsCountry($data['country']);
        }

        return $report;
    }

    private function getProfileUrl($login)
    {
        return 'https://connect.sensiolabs.com/profile/'.$login;
    }


}

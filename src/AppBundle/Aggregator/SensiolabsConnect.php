<?php

namespace AppBundle\Aggregator;

use AppBundle\Aggregator\Helper\SensiolabsDataExtractor;
use AppBundle\Client\PageGetterInterface;
use AppBundle\Helper\ProgressInterface;
use AppBundle\Repository\ContributorRepository;
use GuzzleHttp\Exception\ClientException;

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

            $progress->setMessage($login);

            $url = $this->getProfileUrl($login);

            try {
                $crawler = $this->httpClient->getPageDom($url);
            } catch (ClientException $e) {
                $contributor->setSensiolabsPageError($e->getCode());

                continue;
            }

            $data = $this->extractor->extract($crawler);

            if('' !== $contributor->getGithubLogin() &&
                $data['github_login'] !== $contributor->getGithubLogin()
            ) {
                $report['unmatchedGuthubLogins'][] = sprintf('Id: %d, github login: [%s]',
                    $contributor->getId(), $data['github_login']);

                continue;
            }

            if ('' !== $data['github_login']) {
                $contributor->setGithubLogin($data['github_login']);
            }

            if ('' !== $data['country']) {
                $contributor->setCountry($data['country']);
            }
        }

        $this->repository->flush();

        return $report;
    }

    private function getProfileUrl($login)
    {
        return 'https://connect.sensiolabs.com/profile/'.$login;
    }


}

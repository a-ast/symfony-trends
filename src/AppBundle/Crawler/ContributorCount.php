<?php

namespace AppBundle\Crawler;

use AppBundle\Entity\ProjectVersion;
use AppBundle\Repository\ProjectVersionRepository;
use GuzzleHttp\ClientInterface;

class ContributorCount implements CrawlerInterface
{
    /**
     * @var ClientInterface
     */
    private $httpClient;
    /**
     * @var ProjectVersionRepository
     */
    private $repository;

    /**
     * Constructor.
     *
     * @param ClientInterface $httpClient
     * @param ProjectVersionRepository $repository
     */
    public function __construct(ClientInterface $httpClient, ProjectVersionRepository $repository)
    {
        $this->httpClient = $httpClient;
        $this->repository = $repository;
    }

    public function getData(array $options = [])
    {
        /** @var ProjectVersion[] $versions */
        $versions = $this->repository->findAll();

        foreach ($versions as $version) {
            $count = $this->getContributorCount($version->getLabel());
            $version->setContributorCount($count);

            // @todo: do not flush immediately
            $this->repository->store($version);
        }
    }

    protected function getContributorCount($version)
    {
        $uri = sprintf('https://raw.githubusercontent.com/symfony/symfony/v%s.0/CONTRIBUTORS.md', $version);

        $response = $this->httpClient->request('GET', $uri);

        $responseBody = $response->getBody();

        $fileLines = explode(PHP_EOL, $responseBody);

        $count = 0;

        foreach ($fileLines as $line) {
            if (0 === strpos($line, ' - ')) {
                $count++;
            }
        }

        return $count;
    }
}

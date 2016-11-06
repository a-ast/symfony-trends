<?php

namespace AppBundle\Aggregator;

use AppBundle\Helper\ProgressInterface;
use AppBundle\Repository\ContributorRepository;
use GuzzleHttp\ClientInterface;
use Symfony\Component\DomCrawler\Crawler;

class SensiolabsConnect implements AggregatorInterface
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
     * Constructor.
     *
     * @param ClientInterface $httpClient
     * @param ContributorRepository $repository
     */
    public function __construct(ClientInterface $httpClient, ContributorRepository $repository)
    {
        $this->httpClient = $httpClient;
        $this->repository = $repository;
    }

    public function aggregate(array $options, ProgressInterface $progress = null)
    {
        $logins = $this->repository->findWithSensiolabsLogin();

        foreach ($logins as $login) {
            $pageContent = $this->getPageContents($login);

            $crawler = new Crawler($pageContent);
            $node = $crawler->filterXPath('//p[@itemprop="address"]/span[itemprop="addressLocality"]');
            $city = $node->text();

        }
    }

    private function getPageContents($login)
    {
        $response = $this->httpClient->request('GET', 'https://connect.sensiolabs.com/profile/'.$login);

        $responseBody = $response->getBody();

        return (string)$responseBody;
    }
}

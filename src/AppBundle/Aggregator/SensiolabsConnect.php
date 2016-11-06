<?php

namespace AppBundle\Aggregator;

use AppBundle\Helper\ProgressInterface;
use AppBundle\Repository\ContributorRepository;
use GuzzleHttp\ClientInterface;

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

    }
}

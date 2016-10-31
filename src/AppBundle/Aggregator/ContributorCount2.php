<?php

namespace AppBundle\Aggregator;

use AppBundle\Entity\ProjectVersion;
use AppBundle\Repository\ContributionHistoryRepository;
use AppBundle\Repository\ProjectVersionRepository;
use GuzzleHttp\ClientInterface;

class ContributorCount2 implements AggregatorInterface
{
    private $httpClient;
    /**
     * @var ProjectVersionRepository
     */
    private $versionRepository;
    /**
     * @var ContributionHistoryRepository
     */
    private $contributionRepository;

    /**
     * Constructor.
     *
     * @param ProjectVersionRepository $versionRepository
     * @param ContributionHistoryRepository $contributionRepository
     * @internal param ClientInterface $httpClient
     */
    public function __construct(ProjectVersionRepository $versionRepository, ContributionHistoryRepository $contributionRepository)
    {
        $this->versionRepository = $versionRepository;
        $this->contributionRepository = $contributionRepository;
    }

    public function aggregate(array $options = [])
    {
        $projectId = 1;

        /** @var ProjectVersion[] $versions */
        $versions = $this->versionRepository->findBy(['projectId' => $projectId]);

        foreach ($versions as $version) {
            $count = $this->contributionRepository->getContributorCount($projectId, $version->getStartedAt(),
                $version->getReleasedAt());

            $version->setContributorCount2($count);

            // @todo: do not flush immediately
            $this->versionRepository->store($version);
        }
    }
}

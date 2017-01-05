<?php

namespace AppBundle\Aggregator;

use AppBundle\Builder\ContributorBuilder;
use AppBundle\Client\Github\ClientAdapterInterface;
use AppBundle\Entity\Contribution;
use AppBundle\Entity\Project;
use AppBundle\Helper\ProgressInterface;
use AppBundle\Model\GithubCommit;
use AppBundle\Repository\ContributionRepository;
use AppBundle\Repository\ProjectRepository;

class GithubCommitHistory implements AggregatorInterface
{
    /**
     * @var ClientAdapterInterface
     */
    private $apiClient;

    /**
     * @var ContributorBuilder
     */
    private $contributorBuilder;

    /**
     * @var ContributionRepository
     */
    private $contributionRepository;

    /**
     * @var ProjectRepository
     */
    private $projectRepository;

    /**
     * @var array
     */
    private $maintenanceCommitPatterns;

    /**
     * Constructor.
     *
     * @param ClientAdapterInterface $apiClient
     * @param ContributorBuilder $contributorBuilder
     * @param ProjectRepository $projectRepository
     * @param ContributionRepository $contributionRepository
     * @param array $maintenanceCommitPatterns
     */
    public function __construct(
        ClientAdapterInterface $apiClient,
        ContributorBuilder $contributorBuilder,
        ProjectRepository $projectRepository,
        ContributionRepository $contributionRepository,
        array $maintenanceCommitPatterns)
    {
        $this->apiClient = $apiClient;
        $this->contributorBuilder = $contributorBuilder;
        $this->contributionRepository = $contributionRepository;
        $this->projectRepository = $projectRepository;
        $this->maintenanceCommitPatterns = $maintenanceCommitPatterns;
    }

    /**
     * @inheritdoc
     */
    public function aggregate(array $options, ProgressInterface $progress = null)
    {
        $projectId = $options['project_id'];
        /** @var Project $project */
        $project = $this->projectRepository->find($projectId);

        if (null === $project) {
            throw new \RuntimeException(sprintf('Project %d not found', $projectId));
        }

        $projectRepo = $project->getGithubPath();
        $sinceDate = $this->getSinceDate($projectId);

        foreach ($this->apiClient->getCommits($projectRepo, $sinceDate) as $commit) {
            $contributor = $this->contributorBuilder->buildFromGithubCommit($commit);
            $contribution = $this->createContribution($commit, $projectId, $contributor->getId());

            $this->contributionRepository->clear();
            unset($contributor);
            unset($contribution);
        }
    }

    /**
     * @param $projectId
     *
     * @return \DateTimeImmutable
     */
    protected function getSinceDate($projectId)
    {
        $lastCommitDate = $this->contributionRepository->getLastCommitDate($projectId);
        $sinceDate = $lastCommitDate->modify('+1 sec');

        return $sinceDate;
    }

    /**
     * @param GithubCommit $commit
     * @param int $projectId
     * @param int $contributorId
     *
     * @return Contribution
     */
    private function createContribution(GithubCommit $commit, $projectId, $contributorId)
    {
        $contribution = new Contribution($projectId, $contributorId, $commit->getSha());
        $contribution->setFromGithubCommit($commit, $this->maintenanceCommitPatterns);
        $this->contributionRepository->store($contribution);

        return $contribution;
    }
}

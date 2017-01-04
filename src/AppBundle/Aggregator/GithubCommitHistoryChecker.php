<?php

namespace AppBundle\Aggregator;

use AppBundle\Builder\ContributorBuilder;
use AppBundle\Client\GithubApiClient;
use AppBundle\Entity\Project;
use AppBundle\Helper\ProgressInterface;
use AppBundle\Model\GithubCommit;
use AppBundle\Repository\ContributionRepository;
use AppBundle\Repository\ProjectRepository;

class GithubCommitHistoryChecker implements AggregatorInterface
{
    /**
     * @var GithubApiClient
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
     * @param GithubApiClient $apiClient
     * @param ContributorBuilder $contributorBuilder
     * @param ProjectRepository $projectRepository
     * @param ContributionRepository $contributionRepository
     * @param array $maintenanceCommitPatterns
     */
    public function __construct(
        GithubApiClient $apiClient,
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

        $page = 1;

        while ($commits = $this->apiClient->getCommits($projectRepo, null, $page)) {

            foreach ($commits as $commitData) {

                $commit = new GithubCommit($commitData);

                $existingCommit = $this->contributionRepository->findOneBy(['commitHash' => $commit->getSha()]);
                if (null === $existingCommit) {
                    print $commit->getDate()->format('Y:m:d H:i') .' '. $commit->getSha() . "\n";
                }
            }

            $page++;
        }
    }
}

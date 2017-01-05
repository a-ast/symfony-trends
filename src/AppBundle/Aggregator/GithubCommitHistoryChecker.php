<?php

namespace AppBundle\Aggregator;

use AppBundle\Client\Github\ClientAdapterInterface;
use AppBundle\Entity\Contribution;
use AppBundle\Entity\Contributor;
use AppBundle\Entity\Project;
use AppBundle\Helper\ProgressInterface;
use AppBundle\Model\GithubCommit;
use AppBundle\Repository\ContributionRepository;
use AppBundle\Repository\ContributorRepository;
use AppBundle\Repository\ProjectRepository;
use AppBundle\Util\ArrayUtils;

class GithubCommitHistoryChecker implements AggregatorInterface
{
    /**
     * @var ClientAdapterInterface
     */
    private $apiClient;

    /**
     * @var ContributorRepository
     */
    private $contributorRepository;

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
     * @param ContributorRepository $contributorRepository
     * @param ProjectRepository $projectRepository
     * @param ContributionRepository $contributionRepository
     * @param array $maintenanceCommitPatterns
     */
    public function __construct(
        ClientAdapterInterface $apiClient,
        ContributorRepository $contributorRepository,
        ProjectRepository $projectRepository,
        ContributionRepository $contributionRepository,
        array $maintenanceCommitPatterns)
    {
        $this->apiClient = $apiClient;
        $this->contributorRepository = $contributorRepository;
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

        foreach ($this->apiClient->getCommits($projectRepo, null) as $commit) {
            if(null == $commit->getAuthorId()) {
                print '['.$commit->getCommitAuthorName().'] ['.$commit->getAuthorLogin().']'.PHP_EOL;
            }
        }
    }
}

<?php

namespace AppBundle\Aggregator;

use AppBundle\Client\Github\ClientAdapterInterface;
use AppBundle\Entity\Project;
use AppBundle\Helper\ProgressInterface;
use AppBundle\Repository\ProjectRepository;

class GithubCommitHistoryChecker implements AggregatorInterface
{
    /**
     * @var ClientAdapterInterface
     */
    private $apiClient;

    /**
     * @var ProjectRepository
     */
    private $projectRepository;


    /**
     * Constructor.
     *
     * @param ClientAdapterInterface $apiClient
     * @param ProjectRepository $projectRepository
     */
    public function __construct(
        ClientAdapterInterface $apiClient,
        ProjectRepository $projectRepository)
    {
        $this->apiClient = $apiClient;
        $this->projectRepository = $projectRepository;
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
            if(null === $commit->getAuthorId()) {
                print '['.$commit->getCommitAuthorName().'] ['.$commit->getAuthorLogin().']'.PHP_EOL;
            }
        }
    }
}

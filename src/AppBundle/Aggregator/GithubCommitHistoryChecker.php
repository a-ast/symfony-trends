<?php

namespace AppBundle\Aggregator;

use AppBundle\Client\Github\ClientAdapterInterface;
use AppBundle\Entity\Project;
use AppBundle\Helper\ProgressInterface;

class GithubCommitHistoryChecker implements AggregatorInterface
{
    /**
     * @var ClientAdapterInterface
     */
    private $apiClient;

    /**
     * Constructor.
     *
     * @param ClientAdapterInterface $apiClient
     */
    public function __construct(ClientAdapterInterface $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * @inheritdoc
     */
    public function aggregate(Project $project, array $options, ProgressInterface $progress = null)
    {
        $projectRepo = $project->getGithubPath();

        foreach ($this->apiClient->getCommits($projectRepo, null) as $commit) {
            if(null === $commit->getAuthorId()) {
                print '['.$commit->getCommitAuthorName().'] ['.$commit->getAuthorLogin().']'.PHP_EOL;
            }
        }
    }
}

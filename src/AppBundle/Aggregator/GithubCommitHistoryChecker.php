<?php

namespace AppBundle\Aggregator;

use AppBundle\Client\Github\GithubApiInterface;
use AppBundle\Entity\Project;
use AppBundle\Helper\ProgressInterface;

class GithubCommitHistoryChecker implements AggregatorInterface
{
    /**
     * @var GithubApiInterface
     */
    private $apiClient;

    /**
     * Constructor.
     *
     * @param GithubApiInterface $apiClient
     */
    public function __construct(GithubApiInterface $apiClient)
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

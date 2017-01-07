<?php

namespace AppBundle\Aggregator;

use AppBundle\Client\Github\GithubApiInterface;
use AppBundle\Entity\Project;
use AppBundle\Helper\ProgressInterface;
use AppBundle\Repository\ContributorRepository;

class GithubFork implements AggregatorInterface
{
    /**
     * @var GithubApiInterface
     */
    private $githubApi;
    /**
     * @var ContributorRepository
     */
    private $contributorRepository;

    public function __construct(GithubApiInterface $githubApi, ContributorRepository $contributorRepository)
    {
        // TODO: write logic here
        $this->githubApi = $githubApi;
        $this->contributorRepository = $contributorRepository;
    }

    /**
     * @inheritdoc
     */
    public function aggregate(Project $project, array $options, ProgressInterface $progress = null)
    {
        // TODO: Implement aggregate() method.
    }
}

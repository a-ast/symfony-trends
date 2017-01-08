<?php

namespace AppBundle\Aggregator;

use AppBundle\Client\Github\GithubApiInterface;
use AppBundle\Entity\Fork;
use AppBundle\Entity\Project;
use AppBundle\Helper\ProgressInterface;
use AppBundle\Repository\ForkRepository;

class GithubFork implements AggregatorInterface
{
    /**
     * @var GithubApiInterface
     */
    private $githubApi;

    /**
     * @var ForkRepository
     */
    private $forkRepository;

    /**
     * Constructor.
     * @param GithubApiInterface $githubApi
     * @param ForkRepository $forkRepository
     */
    public function __construct(GithubApiInterface $githubApi, ForkRepository $forkRepository)
    {
        $this->githubApi = $githubApi;
        $this->forkRepository = $forkRepository;
    }

    /**
     * @inheritdoc
     */
    public function aggregate(Project $project, array $options, ProgressInterface $progress = null)
    {
        foreach ($this->githubApi->getForks($project->getGithubPath()) as $githubFork) {

            $fork = new Fork();
            $fork
                ->setProjectId($project->getId())
                ->setGithubId($githubFork->getId())
                ->setOwnerGithubId($githubFork->getOwnerId())
                ->setCreatedAt($githubFork->getCreatedAt())
                ->setUpdatedAt($githubFork->getUpdatedAt())
                ->setPushedAt($githubFork->getPushedAt());

            $this->forkRepository->persist($fork);
        }

        $this->forkRepository->flush();
    }
}

<?php

namespace AppBundle\Aggregator;

use AppBundle\Client\Github\GithubApiInterface;
use AppBundle\Entity\Project;
use AppBundle\Entity\PullRequest as EntityPullRequest;
use AppBundle\Helper\ProgressInterface;
use AppBundle\Repository\PullRequestRepository;
use DateTimeInterface;

class PullRequest implements AggregatorInterface
{
    /**
     * @var GithubApiInterface
     */
    private $githubApi;
    /**
     * @var PullRequestRepository
     */
    private $repository;

    /**
     * Constructor.
     *
     * @param GithubApiInterface $githubApi
     * @param PullRequestRepository $repository
     */
    public function __construct(GithubApiInterface $githubApi, PullRequestRepository $repository)
    {
        $this->githubApi = $githubApi;
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function aggregate(Project $project, array $options, ProgressInterface $progress = null)
    {
        $sinceDate = $this->getSinceDate($project->getId());

        foreach ($this->githubApi->getPullRequests($project->getGithubPath(), $sinceDate) as $pullRequest) {

            $pr = new EntityPullRequest();
            $pr
                ->setProjectId($project->getId())
                ->setGithubId($pullRequest->getId())
                ->setNumber($pullRequest->getNumber())
                ->setState($pullRequest->getState())
                ->setGithubUserId($pullRequest->getUserId())

                ->setTitle($pullRequest->getTitle())
                ->setBody($pullRequest->getBody())

                ->setCreatedAt($pullRequest->getCreatedAt())
                ->setUpdatedAt($pullRequest->getUpdatedAt())
                ->setClosedAt($pullRequest->getClosedAt())
                ->setMergedAt($pullRequest->getMergedAt())

                ->setMergeSha($pullRequest->getMergeSha())
                ->setHeadSha($pullRequest->getHeadSha())
                ->setBaseSha($pullRequest->getBaseSha())

                ->setBaseRef($pullRequest->getBaseRef())
            ;

            print '.';
            $this->repository->persist($pr);
        }

        print PHP_EOL.'Flushing...'.PHP_EOL;
        $this->repository->flush();
    }

    /**
     * @param int $projectId
     *
     * @return DateTimeInterface
     */
    protected function getSinceDate($projectId)
    {
        $lastCommitDate = $this->repository->getLastCreatedAtDate($projectId);
        $sinceDate = $lastCommitDate->modify('+1 sec');

        return $sinceDate;
    }
}

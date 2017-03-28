<?php

namespace Aa\ATrends\Aggregator;

use Aa\ATrends\Aggregator\Options\OptionsInterface;
use Aa\ATrends\Progress\ProgressNotifierAwareTrait;
use Aa\ATrends\Api\Github\GithubApiInterface;
use Aa\ATrends\Entity\PullRequest as EntityPullRequest;
use Aa\ATrends\Repository\PullRequestRepository;
use DateTimeInterface;
use Symfony\Component\Config\Definition\Exception\Exception;

class PullRequestReviewAggregator implements ProjectAwareAggregatorInterface
{
    use ProjectAwareTrait, ProgressNotifierAwareTrait;

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
    public function aggregate(OptionsInterface $options)
    {
        $count = 0;

        foreach ($this->repository->findAllPullRequests($this->getProject()->getId()) as $pullRequest) {
            $reviews = $this->githubApi->getPullRequestReviews($this->getProject()->getGithubPath(), $pullRequest->getId());

            $count++;
        }

        print $count;
    }
}

<?php

namespace spec\Aa\ATrends\Aggregator;

use Aa\ATrends\Aggregator\ProjectAwareAggregatorInterface;
use Aa\ATrends\Aggregator\PullRequestReviewAggregator;
use Aa\ATrends\Api\Github\GithubApiInterface;
use Aa\ATrends\Repository\PullRequestRepository;
use Aa\ATrends\Repository\PullRequestReviewRepository;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin PullRequestReviewAggregator
 */
class PullRequestReviewAggregatorSpec extends ObjectBehavior
{
    function it_is_initializable(GithubApiInterface $githubApi, PullRequestRepository $pullRequestRepository, PullRequestReviewRepository $reviewRepository)
    {
        $this->beConstructedWith($githubApi, $pullRequestRepository, $reviewRepository);
        $this->shouldHaveType(PullRequestReviewAggregator::class);
        $this->shouldImplement(ProjectAwareAggregatorInterface::class);
    }
}

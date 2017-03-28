<?php

namespace spec\Aa\ATrends\Aggregator;

use Aa\ATrends\Aggregator\ProjectAwareAggregatorInterface;
use Aa\ATrends\Aggregator\PullRequestReviewAggregator;
use Aa\ATrends\Api\Github\GithubApiInterface;
use Aa\ATrends\Repository\PullRequestRepository;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin PullRequestReviewAggregator
 */
class PullRequestReviewAggregatorSpec extends ObjectBehavior
{
    function it_is_initializable(GithubApiInterface $githubApi, PullRequestRepository $repository)
    {
        $this->beConstructedWith($githubApi, $repository);
        $this->shouldHaveType(PullRequestReviewAggregator::class);
        $this->shouldImplement(ProjectAwareAggregatorInterface::class);
    }
}

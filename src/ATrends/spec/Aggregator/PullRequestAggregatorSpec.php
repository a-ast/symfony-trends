<?php

namespace spec\Aa\ATrends\Aggregator;

use Aa\ATrends\Aggregator\PullRequestBodyProcessor;
use Aa\ATrends\Aggregator\ProjectAwareAggregatorInterface;
use Aa\ATrends\Aggregator\PullRequestAggregator;
use Aa\ATrends\Api\Github\GithubApiInterface;
use AppBundle\Repository\PullRequestRepository;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin PullRequestAggregator
 */
class PullRequestAggregatorSpec extends ObjectBehavior
{
    function it_is_initializable(GithubApiInterface $githubApi, PullRequestRepository $repository, PullRequestBodyProcessor $bodyProcessor)
    {
        $this->beConstructedWith($githubApi, $repository, $bodyProcessor);
        $this->shouldHaveType(PullRequestAggregator::class);
        $this->shouldImplement(ProjectAwareAggregatorInterface::class);
    }
}

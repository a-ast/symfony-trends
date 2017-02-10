<?php

namespace spec\AppBundle\Aggregator;

use AppBundle\Aggregator\AggregatorInterface;
use AppBundle\Aggregator\Helper\PullRequestBodyProcessor;
use AppBundle\Aggregator\ProjectAwareAggregatorInterface;
use AppBundle\Aggregator\PullRequestAggregator;
use AppBundle\Client\Github\GithubApiInterface;
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

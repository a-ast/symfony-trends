<?php

namespace spec\AppBundle\Aggregator;

use AppBundle\Aggregator\AggregatorInterface;
use AppBundle\Aggregator\PullRequest;
use AppBundle\Client\Github\GithubApiInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin PullRequest
 */
class PullRequestSpec extends ObjectBehavior
{
    function it_is_initializable(GithubApiInterface $githubApi)
    {
        $this->beConstructedWith($githubApi);
        $this->shouldHaveType(PullRequest::class);
        $this->shouldImplement(AggregatorInterface::class);
    }
}

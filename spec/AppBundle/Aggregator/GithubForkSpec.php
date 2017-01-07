<?php

namespace spec\AppBundle\Aggregator;

use AppBundle\Aggregator\AggregatorInterface;
use AppBundle\Aggregator\GithubFork;
use AppBundle\Client\Github\GithubApiInterface;
use AppBundle\Entity\Project;
use AppBundle\Repository\ContributorRepository;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin GithubFork
 */
class GithubForkSpec extends ObjectBehavior
{
    function let(GithubApiInterface $githubApi, ContributorRepository $contributorRepository)
    {
        $this->beConstructedWith($githubApi, $contributorRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(GithubFork::class);
        $this->shouldImplement(AggregatorInterface::class);
    }

    function it_returns_aggregated_data(GithubApiInterface $githubApi,
        ContributorRepository $contributorRepository,
        Project $project)
    {
        $this->aggregate($project, []);
    }
}

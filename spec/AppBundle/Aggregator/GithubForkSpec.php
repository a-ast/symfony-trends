<?php

namespace spec\AppBundle\Aggregator;

use AppBundle\Aggregator\AggregatorInterface;
use AppBundle\Aggregator\GithubFork;
use AppBundle\Entity\Fork;
use AppBundle\Model\GithubFork as ModelGithubFork;
use AppBundle\Client\Github\GithubApiInterface;
use AppBundle\Entity\Project;
use AppBundle\Repository\ForkRepository;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin GithubFork
 */
class GithubForkSpec extends ObjectBehavior
{
    function let(GithubApiInterface $githubApi, ForkRepository $forkRepository)
    {
        $this->beConstructedWith($githubApi, $forkRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(GithubFork::class);
        $this->shouldImplement(AggregatorInterface::class);
    }

    function it_returns_aggregated_data(Project $project, GithubApiInterface $githubApi, ForkRepository $forkRepository)
    {
        $this->initDependencies($githubApi, $forkRepository);

        $project
            ->getId()
            ->willReturn(1);

        $project
            ->getGithubPath()
            ->willReturn('valinor/path');


        $this->aggregate($project, []);
    }

    /**
     * @param GithubApiInterface $githubApi
     * @param ForkRepository $forkRepository
     */
    protected function initDependencies(GithubApiInterface $githubApi, ForkRepository $forkRepository)
    {
        $fork = new ModelGithubFork(1, 100, new \DateTimeImmutable(), new \DateTimeImmutable(),
            new \DateTimeImmutable());

        $githubApi
            ->getForks('valinor/path')
            ->willReturn([$fork]);

        $forkRepository
            ->persist(Argument::type(Fork::class))
            ->shouldBeCalled();

        $forkRepository
            ->flush()
            ->shouldBeCalled();
    }
}

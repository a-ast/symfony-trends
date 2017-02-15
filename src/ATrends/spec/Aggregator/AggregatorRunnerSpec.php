<?php

namespace spec\Aa\ATrends\Aggregator;

use Aa\ATrends\Aggregator\AggregatorInterface;
use Aa\ATrends\Aggregator\AggregatorOptionsInterface;
use Aa\ATrends\Aggregator\AggregatorRunner;
use Aa\ATrends\Model\ProjectInterface;
use Aa\ATrends\Progress\ProgressInterface;
use Aa\ATrends\Repository\ProjectRepository;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin AggregatorRunner
 */
class AggregatorRunnerSpec extends ObjectBehavior
{
    function let(ProjectRepository $repository, ProjectInterface $project)
    {
        $repository->findAll()->willReturn([$project]);

        $this->beConstructedWith($repository);
    }

//    function it_is_initializable()
//    {
//        $this->shouldHaveType(AggregatorRunner::class);
//    }
//
//    public function it_runs_aggregator(AggregatorInterface $aggregator)
//    {
//        $this->run($aggregator);
//    }
}

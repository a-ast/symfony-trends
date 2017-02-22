<?php

namespace spec\Aa\ATrends\Aggregator;

use Aa\ATrends\Aggregator\AggregatorInterface;
use Aa\ATrends\Aggregator\Options\OptionsInterface;
use Aa\ATrends\Aggregator\Runner\Runner;
use Aa\ATrends\Aggregator\Report\ReportInterface;
use Aa\ATrends\Event\ProgressFinishEvent;
use Aa\ATrends\Event\ProgressStartEvent;
use Aa\ATrends\Model\ProjectInterface;
use Aa\ATrends\Repository\ProjectRepository;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @mixin Runner
 */
class RunnerSpec extends ObjectBehavior
{
    function let(ProjectRepository $repository, EventDispatcherInterface $dispatcher, ProjectInterface $project)
    {
        $repository->findAll()->willReturn([$project]);

        $this->beConstructedWith($repository, $dispatcher);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Runner::class);
    }

    public function it_runs_aggregator(
        AggregatorInterface $aggregator,
        OptionsInterface $options,
        ReportInterface $report,
        EventDispatcherInterface $dispatcher
    ) {
        $dispatcher->dispatch(ProgressStartEvent::NAME, Argument::type(ProgressStartEvent::class))->shouldBeCalled();
        $dispatcher->dispatch(ProgressFinishEvent::NAME, Argument::type(ProgressFinishEvent::class))->shouldBeCalled();

        $aggregator->aggregate($options)->willReturn($report);

        $this->run($aggregator, $options)->shouldReturn($report);
    }
}

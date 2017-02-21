<?php

namespace spec\Aa\ATrends\Aggregator;

use Aa\ATrends\Aggregator\AggregatorInterface;
use Aa\ATrends\Aggregator\Options\OptionsInterface;
use Aa\ATrends\Aggregator\AggregatorRunner;
use Aa\ATrends\Event\ProgressFinishEvent;
use Aa\ATrends\Event\ProgressStartEvent;
use Aa\ATrends\Model\ProjectInterface;
use Aa\ATrends\Repository\ProjectRepository;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @mixin AggregatorRunner
 */
class AggregatorRunnerSpec extends ObjectBehavior
{
    function let(ProjectRepository $repository, EventDispatcherInterface $dispatcher, ProjectInterface $project)
    {
        $repository->findAll()->willReturn([$project]);

        $this->beConstructedWith($repository, $dispatcher);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(AggregatorRunner::class);
    }

    public function it_runs_aggregator(
        AggregatorInterface $aggregator,
        OptionsInterface $options,
        EventDispatcherInterface $dispatcher
    ) {
        $dispatcher->dispatch(ProgressStartEvent::NAME, Argument::type(ProgressStartEvent::class))->shouldBeCalled();
        $dispatcher->dispatch(ProgressFinishEvent::NAME, Argument::type(ProgressFinishEvent::class))->shouldBeCalled();

        $this->run($aggregator, $options);
    }
}

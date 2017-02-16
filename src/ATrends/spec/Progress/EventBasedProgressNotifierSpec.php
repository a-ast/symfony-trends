<?php

namespace spec\Aa\ATrends\Progress;

use Aa\ATrends\Aggregator\AggregatorInterface;
use Aa\ATrends\Event\ProgressAdvanceEvent;
use Aa\ATrends\Progress\EventBasedProgressNotifier;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @mixin EventBasedProgressNotifier
 */
class EventBasedProgressNotifierSpec extends ObjectBehavior
{
    function let(EventDispatcherInterface $dispatcher)
    {
        $this->beConstructedWith($dispatcher);
        $this->setInitiator('initiator');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(EventBasedProgressNotifier::class);
    }

    public function it_dispatches_progress_advance_event(EventDispatcherInterface $dispatcher, ProgressAdvanceEvent $event)
    {
        $event = new ProgressAdvanceEvent('initiator', 10);

        $dispatcher->dispatch(ProgressAdvanceEvent::NAME, $event)->shouldBeCalled();
        $this->advance(10);
    }
}

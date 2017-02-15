<?php

namespace spec\Aa\ATrends\Progress;

use Aa\ATrends\Event\ProgressAdvanceEvent;
use Aa\ATrends\Progress\ProgressNotifier;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @mixin ProgressNotifier
 */
class ProgressNotifierSpec extends ObjectBehavior
{
    function let(EventDispatcherInterface $dispatcher)
    {
        $this->beConstructedWith($dispatcher);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ProgressNotifier::class);
    }

    public function it_dispatches_progress_advance_event(EventDispatcherInterface $dispatcher, ProgressAdvanceEvent $event)
    {
        $event = new ProgressAdvanceEvent(10);

        $dispatcher->dispatch(ProgressAdvanceEvent::NAME, $event)->shouldBeCalled();
        $this->advanceProgress(10);
    }
}

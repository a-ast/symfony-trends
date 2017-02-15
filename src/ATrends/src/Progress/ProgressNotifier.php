<?php

namespace Aa\ATrends\Progress;

use Aa\ATrends\Event\ProgressAdvanceEvent;
use Aa\ATrends\Event\ProgressMessageEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ProgressNotifier
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher = null)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param int $step
     */
    public function advanceProgress($step = 1)
    {
        if (null !== $this->dispatcher) {
            $this->dispatcher->dispatch(ProgressAdvanceEvent::NAME, new ProgressAdvanceEvent($step));
        }
    }

    /**
     * @param string $message
     */
    public function setProgressMessage($message)
    {
        if (null !== $this->dispatcher) {
            $this->dispatcher->dispatch(ProgressMessageEvent::NAME, new ProgressMessageEvent($message));
        }
    }
}

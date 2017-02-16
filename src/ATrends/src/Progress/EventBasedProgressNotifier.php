<?php

namespace Aa\ATrends\Progress;

use Aa\ATrends\Event\ProgressAdvanceEvent;
use Aa\ATrends\Event\ProgressFinishEvent;
use Aa\ATrends\Event\ProgressMessageEvent;
use Aa\ATrends\Event\ProgressStartEvent;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EventBasedProgressNotifier implements ProgressNotifierInterface
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var mixed
     */
    private $initiator;

    public function __construct(EventDispatcherInterface $dispatcher = null)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @inheritdoc
     */
    public function advance($step = 1)
    {
        $this->dispatchEvent(ProgressAdvanceEvent::NAME, new ProgressAdvanceEvent($this->initiator, $step));
    }

    /**
     * @inheritdoc
     */
    public function setMessage($message)
    {
        $this->dispatchEvent(ProgressMessageEvent::NAME, new ProgressMessageEvent($this->initiator, $message));
    }

    /**
     * @inheritdoc
     */
    public function start()
    {
        $this->dispatchEvent(ProgressStartEvent::NAME, new ProgressStartEvent($this->initiator));
    }

    /**
     * @inheritdoc
     */
    public function finish()
    {
        $this->dispatchEvent(ProgressFinishEvent::NAME, new ProgressFinishEvent($this->initiator));
    }

    /**
     * @param $initiator
     */
    public function setInitiator($initiator)
    {
        $this->initiator = $initiator;
    }

    private function dispatchEvent($name, Event $event)
    {
        if (null !== $this->dispatcher) {
            $this->dispatcher->dispatch($name, $event);
        }
    }
}

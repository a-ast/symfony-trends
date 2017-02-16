<?php


namespace Aa\ATrends\Event;

use Symfony\Component\EventDispatcher\Event;

abstract class ProgressEvent extends Event
{
    /**
     * @var mixed
     */
    private $initiator;

    /**
     * Constructor.
     *
     * @param $initiator
     */
    public function __construct($initiator)
    {
        $this->initiator = $initiator;
    }

    /**
     * @return mixed
     */
    public function getInitiator()
    {
        return $this->initiator;
    }
}

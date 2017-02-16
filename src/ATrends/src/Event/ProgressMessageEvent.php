<?php

namespace Aa\ATrends\Event;

class ProgressMessageEvent extends ProgressEvent
{
    const NAME = 'trends.progress.message';

    /**
     * @var string
     */
    private $message;

    /**
     * Constructor.
     *
     * @param mixed $initiator
     * @param string $message
     */
    public function __construct($initiator, $message)
    {
        parent::__construct($initiator);

        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }
}

<?php


namespace Aa\ATrends\Event;

use Symfony\Component\EventDispatcher\Event;

class ProgressMessageEvent extends Event
{
    const NAME = 'trends.message';

    /**
     * @var string
     */
    private $message;

    /**
     * Constructor.
     *
     * @param string $message
     */
    public function __construct($message)
    {
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

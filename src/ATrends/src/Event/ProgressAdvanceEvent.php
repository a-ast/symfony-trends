<?php


namespace Aa\ATrends\Event;

use Symfony\Component\EventDispatcher\Event;

class ProgressAdvanceEvent extends Event
{
    const NAME = 'trends.advance';

    /**
     * @var int
     */
    private $advanceStep;

    /**
     * Constructor.
     *
     * @param int $step
     */
    public function __construct($step)
    {
        $this->advanceStep = $step;
    }

    /**
     * @return int
     */
    public function getAdvanceStep()
    {
        return $this->advanceStep;
    }
}

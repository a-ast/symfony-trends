<?php


namespace Aa\ATrends\Event;

class ProgressAdvanceEvent extends ProgressEvent
{
    const NAME = 'trends.progress.advance';

    /**
     * @var int
     */
    private $advanceStep;

    /**
     * Constructor.
     *
     * @param mixed $initiator
     * @param int $step
     */
    public function __construct($initiator, $step)
    {
        parent::__construct($initiator);

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

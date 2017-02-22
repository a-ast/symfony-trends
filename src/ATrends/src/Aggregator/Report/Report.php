<?php

namespace Aa\ATrends\Aggregator\Report;

class Report implements ReportInterface
{
    /**
     * @var int
     */
    private $count = 0;

    /**
     * @inheritdoc
     */
    public function setProcessedItemCount($count)
    {
        $this->count = $count;
    }

    /**
     * @inheritdoc
     */
    public function getProcessedItemCount()
    {
        return $this->count;
    }
}

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
    public function setProcessedRecordCount($count)
    {
        $this->count = $count;
    }

    /**
     * @inheritdoc
     */
    public function getProcessedRecordCount()
    {
        return $this->count;
    }
}

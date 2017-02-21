<?php

namespace Aa\ATrends\Aggregator\Report;

interface ReportInterface
{
    /**
     * @return integer
     */
    public function getProcessedRecordCount();

    /**
     * @param int $count
     *
     * @return void
     */
    public function setProcessedRecordCount($count);
}

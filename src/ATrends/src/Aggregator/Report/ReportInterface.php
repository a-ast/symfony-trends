<?php

namespace Aa\ATrends\Aggregator\Report;

interface ReportInterface
{
    /**
     * @return integer
     */
    public function getProcessedItemCount();

    /**
     * @param int $count
     *
     * @return void
     */
    public function setProcessedItemCount($count);
}

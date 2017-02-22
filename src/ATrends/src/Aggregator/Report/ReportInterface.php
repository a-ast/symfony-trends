<?php

namespace Aa\ATrends\Aggregator\Report;

interface ReportInterface
{
    /**
     * @return integer
     */
    public function getProcessedItemCount();
}

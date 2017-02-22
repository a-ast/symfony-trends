<?php

namespace Aa\ATrends\Aggregator\Report;

interface ReportAwareInterface
{
    /**
     * @return ReportInterface
     */
    public function getReport();
}

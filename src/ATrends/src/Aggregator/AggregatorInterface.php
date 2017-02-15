<?php

namespace Aa\ATrends\Aggregator;

use Aa\ATrends\Progress\ProgressInterface;

interface AggregatorInterface
{
    /**
     * @param AggregatorOptionsInterface $options
     * @param ProgressInterface $progress
     *
     * @return AggregatorReportInterface
     */
    function aggregate(AggregatorOptionsInterface $options, ProgressInterface $progress);
}

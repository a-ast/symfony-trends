<?php

namespace Aa\ATrends\Aggregator;

use Aa\ATrends\Progress\ProgressInterface;

interface AggregatorInterface
{
    /**
     * @param AggregatorOptionsInterface $options
     *
     * @return AggregatorReportInterface
     */
    function aggregate(AggregatorOptionsInterface $options);
}

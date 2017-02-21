<?php

namespace Aa\ATrends\Aggregator;

use Aa\ATrends\Aggregator\Options\AggregatorOptionsInterface;
use Aa\ATrends\Aggregator\Report\AggregatorReportInterface;

interface AggregatorInterface
{
    /**
     * @param AggregatorOptionsInterface $options
     *
     * @return AggregatorReportInterface
     */
    public function aggregate(AggregatorOptionsInterface $options);
}

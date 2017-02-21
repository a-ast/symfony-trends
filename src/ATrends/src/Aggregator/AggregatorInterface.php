<?php

namespace Aa\ATrends\Aggregator;

use Aa\ATrends\Aggregator\Options\AggregatorOptionsInterface;

interface AggregatorInterface
{
    /**
     * @param AggregatorOptionsInterface $options
     *
     * @return AggregatorReportInterface
     */
    public function aggregate(AggregatorOptionsInterface $options);
}

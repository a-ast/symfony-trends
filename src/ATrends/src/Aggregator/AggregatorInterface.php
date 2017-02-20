<?php

namespace Aa\ATrends\Aggregator;

use Aa\ATrends\Progress\ProgressNotifierInterface;

interface AggregatorInterface
{
    /**
     * @param AggregatorOptionsInterface $options
     *
     * @return AggregatorReportInterface
     */
    public function aggregate(AggregatorOptionsInterface $options);
}

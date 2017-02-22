<?php

namespace Aa\ATrends\Aggregator;

use Aa\ATrends\Aggregator\Options\OptionsInterface;
use Aa\ATrends\Aggregator\Report\ReportInterface;

interface AggregatorInterface
{
    /**
     * @param OptionsInterface $options
     *
     * @return void
     */
    public function aggregate(OptionsInterface $options);
}

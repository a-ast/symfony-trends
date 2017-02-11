<?php

namespace Aa\ATrends\Aggregator;

use Aa\ATrends\Progress\ProgressInterface;

interface AggregatorInterface extends BaseAggregatorInterface
{
    /**
     * @param array $options
     * @param ProgressInterface $progress
     *
     * @return array
     */
    function aggregate(array $options, ProgressInterface $progress = null);
}

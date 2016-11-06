<?php

namespace AppBundle\Aggregator;

use AppBundle\Helper\ProgressInterface;

interface AggregatorInterface
{
    /**
     * @param array $options
     * @param ProgressInterface $progress
     *
     * @return array
     */
    public function aggregate(array $options, ProgressInterface $progress = null);
}

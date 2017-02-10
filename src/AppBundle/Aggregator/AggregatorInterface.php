<?php

namespace AppBundle\Aggregator;

use AppBundle\Entity\Project;
use AppBundle\Helper\ProgressInterface;

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

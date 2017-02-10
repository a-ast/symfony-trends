<?php

namespace AppBundle\Aggregator;

use AppBundle\Helper\ProgressInterface;
use AppBundle\Model\ProjectInterface;

interface ProjectAwareAggregatorInterface extends BaseAggregatorInterface
{
    /**
     * @param ProjectInterface $project
     * @param array $options
     * @param ProgressInterface|null $progress
     *
     * @return mixed
     */
    function aggregate(ProjectInterface $project, array $options, ProgressInterface $progress = null);
}

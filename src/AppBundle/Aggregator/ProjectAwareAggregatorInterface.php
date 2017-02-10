<?php

namespace AppBundle\Aggregator;

use AppBundle\Entity\Project;
use AppBundle\Helper\ProgressInterface;

interface ProjectAwareAggregatorInterface extends BaseAggregatorInterface
{
    /**
     * @param Project $project
     * @param array $options
     * @param ProgressInterface|null $progress
     *
     * @return mixed
     */
    function aggregate(Project $project, array $options, ProgressInterface $progress = null);
}

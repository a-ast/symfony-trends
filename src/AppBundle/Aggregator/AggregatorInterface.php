<?php

namespace AppBundle\Aggregator;

use AppBundle\Entity\Project;
use AppBundle\Helper\ProgressInterface;

interface AggregatorInterface
{
    /**
     * @param Project $project
     * @param array $options
     * @param ProgressInterface $progress
     *
     * @return array
     */
    public function aggregate(Project $project, array $options, ProgressInterface $progress = null);
}

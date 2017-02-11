<?php

namespace Aa\ATrends\Aggregator;

use Aa\ATrends\Model\ProjectInterface;
use Aa\ATrends\Progress\ProgressInterface;

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

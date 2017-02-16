<?php

namespace Aa\ATrends\Aggregator;

use Aa\ATrends\Model\ProjectInterface;

interface ProjectAwareAggregatorInterface extends AggregatorInterface
{
    /**
     * @param ProjectInterface $project
     *
     * @return void
     */
    function setProject(ProjectInterface $project);

    /**
     * @return ProjectInterface
     */
    function getProject();
}

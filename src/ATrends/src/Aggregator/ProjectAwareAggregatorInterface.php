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
    public function setProject(ProjectInterface $project);

    /**
     * @return ProjectInterface
     */
    public function getProject();
}

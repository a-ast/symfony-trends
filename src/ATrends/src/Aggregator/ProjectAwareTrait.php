<?php

namespace Aa\ATrends\Aggregator;

use Aa\ATrends\Model\ProjectInterface;

trait ProjectAwareTrait
{
    /**
     * @var ProjectInterface
     */
    private $project;

    /**
     * @param ProjectInterface $project
     */
    function setProject(ProjectInterface $project)
    {
        $this->project = $project;
    }

    /**
     * @return ProjectInterface
     */
    public function getProject()
    {
        return $this->project;
    }
}

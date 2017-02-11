<?php

namespace Aa\ATrends\Repository;

use Aa\ATrends\Entity\Project;
use Doctrine\ORM\EntityRepository;

/**
 * ProjectRepository
 */
class ProjectRepository extends EntityRepository
{
    /**
     * @param array $labels
     *
     * @return Project[]
     */
    public function findByLabel(array $labels)
    {
        $projects = [];

        foreach ($labels as $label) {
            $project = $this->findOneBy(['label' => $label]);

            if (null === $project) {
                throw new \InvalidArgumentException(sprintf('Project with label/alias %s does not exist.', $label));
            }

            $projects[] = $project;
        }

        return $projects;
    }
}

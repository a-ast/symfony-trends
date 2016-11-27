<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Project;
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
    public function getProjectsByLabels(array $labels)
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.label in (:labels)')
            ->setParameter('labels', $labels)
        ;

        $result = $qb->getQuery()->getResult();

        $projects = [];
        /** @var Project $project */
        foreach ($result as &$project) {
            $projects[$project->getId()] = $project;
        }

        return $projects;
    }
}

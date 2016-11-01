<?php

namespace AppBundle\Repository;

use DateTime;

/**
 * Contribution log entries
 */
class ContributionLogRepository extends Repository
{
    /**
     * @param int $projectId
     * @param DateTime $startedAt
     * @param DateTime $finishedAt
     *
     * @return int
     */
    public function getContributorCount($projectId, DateTime $startedAt, DateTime $finishedAt)
    {
        $qb = $this->createQueryBuilder('c')
            ->select('count(distinct c.contributorId)')
            ->where('c.projectId = :projectId')
            ->andWhere('c.commitedAt > :startedAt')
            ->andWhere('c.commitedAt <= :finishedAt')
            ->setParameter('projectId', $projectId)
            ->setParameter('startedAt', $startedAt)
            ->setParameter('finishedAt', $finishedAt)
        ;

        $result = $qb->getQuery()->getSingleResult();

        return (int)$result[1];
    }
}

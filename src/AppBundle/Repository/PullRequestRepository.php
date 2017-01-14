<?php

namespace AppBundle\Repository;

use DateTimeImmutable;

/**
 * PullRequestRepository.
 */
class PullRequestRepository extends Repository
{
    /**
     * @param int $projectId
     *
     * @return DateTimeImmutable
     */
    public function getLastCreatedAtDate($projectId)
    {
        $qb = $this->createQueryBuilder('c')
            ->select('MAX(c.createdAt)')
            ->where('c.projectId = :id')
            ->setParameter('id', $projectId);

        $result = $qb->getQuery()->getSingleScalarResult();

        if (null === $result) {
            $result = '1970-01-01 00:00:00';
        }

        return new DateTimeImmutable($result);
    }
}

<?php

namespace AppBundle\Repository;

use DateTimeImmutable;

/**
 * IssueRepository.
 */
class IssueRepository extends Repository
{
    /**
     * @param int $projectId
     *
     * @return DateTimeImmutable
     */
    public function getLastCreatedAt($projectId)
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

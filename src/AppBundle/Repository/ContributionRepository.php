<?php

namespace AppBundle\Repository;

use AppBundle\Util\DateUtils;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * ContributionRepository
 */
class ContributionRepository extends Repository
{
    /**
     * @param int $projectId
     *
     * @return DateTimeImmutable
     */
    public function getLastCommitDate($projectId)
    {
        $qb = $this->createQueryBuilder('c')
            ->select('MAX(c.commitedAt)')
            ->where('c.projectId = :id')
            ->setParameter('id', $projectId);

        $result = $qb->getQuery()->getSingleScalarResult();

        return new DateTimeImmutable($result);
    }
}

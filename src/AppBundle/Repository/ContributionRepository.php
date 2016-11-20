<?php

namespace AppBundle\Repository;

use DateTime;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * ContributionRepository
 */
class ContributionRepository extends Repository
{
    /**
     * @return array
     */
    public function getContributionIntersection()
    {
        $rsm = new ResultSetMapping();
        $rsm
            ->addScalarResult('project_ids', 'project_ids')
            ->addScalarResult('cnt', 'cnt');

        $query = $this
                    ->getEntityManager()
                    ->createNativeQuery('select project_ids, count(*) as cnt
                        from (
                            select contributor_id, group_concat(project_id) as project_ids
                            from contribution
                            group by contributor_id
                        )
                        group by project_ids', $rsm);

        $result = $query->getResult();

        return $result;
    }

    /**
     * @param int $projectId
     *
     * @return array
     */
    public function getContributorsCommitCounts($projectId)
    {
        $qb = $this->createQueryBuilder('c')
            ->select('c.contributorId', 'c.commitCount')
            ->andWhere('c.projectId = :projectId')
            ->setParameter('projectId', $projectId);

        $result = $qb->getQuery()->getArrayResult();

        return $result;
    }

    /**
     * @return array
     */
    public function getContributionsPerDate()
    {
        $rsm = new ResultSetMapping();
        $rsm
            ->addScalarResult('date', 'date')
            ->addScalarResult('project_id', 'project_id')
            ->addScalarResult('value', 'value');

        $query = $this
            ->getEntityManager()
            ->createNativeQuery('select date(first_commit_at) as date, project_id, count(*) as value
                            from contribution
                            group by date(first_commit_at), project_id
                            order by first_commit_at asc', $rsm);

        $result = $query->getResult();

        return $result;
    }

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

    /**
     * @param int $projectId
     *
     * @return DateTime
     */
    public function getLastCommitDate($projectId)
    {
        $qb = $this->createQueryBuilder('c')
            ->select('MAX(c.commitedAt)')
            ->where('c.projectId = :id')
            ->setParameter('id', $projectId);

        $result = $qb->getQuery()->getSingleScalarResult();

        return new \DateTimeImmutable($result);
    }
}

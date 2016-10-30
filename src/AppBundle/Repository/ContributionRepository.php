<?php

namespace AppBundle\Repository;

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
     * @param int $projectId
     *
     * @return array
     */
    public function getContributionsPerDate($projectId)
    {
        $rsm = new ResultSetMapping();
        $rsm
            ->addScalarResult('date', 'date')
            ->addScalarResult('value', 'value');

        $query = $this
            ->getEntityManager()
            ->createNativeQuery('select date(first_commit_at) as date, count(*) as value
                            from contribution
                            where project_id = :projectId
                            group by date(first_commit_at)
                            order by first_commit_at asc', $rsm)
            ->setParameter('projectId', $projectId);

        $result = $query->getResult();

        return $result;
    }

}

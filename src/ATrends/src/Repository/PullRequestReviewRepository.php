<?php

namespace Aa\ATrends\Repository;

/**
 * PullRequestReviewRepository
 */
class PullRequestReviewRepository extends Repository
{
    public function removeByPullRequestId($id)
    {
        $this->createQueryBuilder('r')
            ->delete()
            ->where('r.pullRequestId = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->execute();
    }
}

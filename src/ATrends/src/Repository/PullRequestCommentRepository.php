<?php

namespace Aa\ATrends\Repository;

/**
 * PullRequestCommentRepository
 */
class PullRequestCommentRepository extends Repository
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

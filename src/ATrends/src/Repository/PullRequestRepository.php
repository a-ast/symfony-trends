<?php

namespace Aa\ATrends\Repository;

use Aa\ATrends\Entity\PullRequest;

/**
 * PullRequestRepository.
 */
class PullRequestRepository extends Repository
{
    /**
     * @param int $projectId
     *
     * @return PullRequest[]
     */
    public function findAllPullRequests($projectId)
    {
        $offset = 0;
        $qb = $this->createQueryBuilder('p')
            ->where('p.projectId = :projectId')
            ->setParameter('projectId', $projectId)
            ->setMaxResults(1000);

        do {
            $qb->setFirstResult($offset);
            $offset += 1000;

            $results = $qb->getQuery()->getResult();

            foreach ($results as $item) {
                yield $item;
            }

        } while (count($results) > 0);
    }
}

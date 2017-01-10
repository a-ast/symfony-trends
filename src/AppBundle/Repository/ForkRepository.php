<?php

namespace AppBundle\Repository;

use AppBundle\Util\ArrayUtils;

/**
 * ForkRepository
 */
class ForkRepository extends Repository
{
    /**
     * @return int[]
     */
    public function findGithubIds()
    {
        $qb = $this->createQueryBuilder('c')
            ->select('c.githubId');

        $result = $qb->getQuery()->getArrayResult();

        return array_column($result, 'githubId');
    }
}

<?php


namespace Tests\AppBundle\Helper;

use Doctrine\ORM\EntityRepository;

class RepositoryUtils
{
    /**
     * @param EntityRepository $repository
     *
     * @return integer
     */
    public static function getRecordCount(EntityRepository $repository)
    {
        $qb = $repository->createQueryBuilder('data');
        $count = (int)$qb->select('COUNT(data)')->getQuery()->getSingleScalarResult();

        return $count;
    }
}

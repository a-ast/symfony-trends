<?php


namespace Tests\AppBundle\Helper;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\PropertyAccess\PropertyAccess;

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

    /**
     * @param EntityRepository $repository
     * @param string $keyProperty
     *
     * @return array
     */
    public static function fetchAll(EntityRepository $repository, $keyProperty)
    {
        $qb = $repository->createQueryBuilder('data');
        $entities = $qb->select('data')
            ->getQuery()
            ->getResult();

        $accessor = PropertyAccess::createPropertyAccessor();

        $result = [];
        foreach ($entities as $entity) {
            $keyPropertyValue = $accessor->getValue($entity, $keyProperty);

            $result[$keyPropertyValue] = $entity;
        }

        return $result;
    }
}

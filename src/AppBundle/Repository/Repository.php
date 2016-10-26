<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Repository
 */
abstract class Repository extends EntityRepository
{
    public function store($object = null)
    {
        if(null !== $object) {
            $this->getEntityManager()->persist($object);
        }

        $this->getEntityManager()->flush($object);
    }

    public function remove($object = null)
    {
        $this->getEntityManager()->remove($object);
        $this->getEntityManager()->flush($object);
    }
}

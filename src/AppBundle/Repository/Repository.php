<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Repository
 */
abstract class Repository extends EntityRepository
{
    /**
     * @param null $object
     *
     * @deprecated Use a combination of persist/flush
     */
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

    public function persist($object)
    {
        $this->getEntityManager()->persist($object);
    }

    public function flush($object = null)
    {
        $this->getEntityManager()->flush($object);
    }

    public function clear()
    {
        $this->getEntityManager()->clear();
    }
}

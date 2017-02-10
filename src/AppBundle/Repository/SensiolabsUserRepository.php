<?php

namespace AppBundle\Repository;

/**
 * ProjectRepository
 */
class SensiolabsUserRepository extends Repository
{
    public function getExistingLogins(array $logins)
    {
        $qb = $this->createQueryBuilder('s')
            ->select('s.login')
            ->where('s.login IN (:logins)')
            ->setParameter('logins', $logins);

        $result = $qb->getQuery()->getArrayResult();

        return array_column($result, 'login');
    }
}

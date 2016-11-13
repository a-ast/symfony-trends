<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Contributor;

/**
 * ContributorRepository
 */
class ContributorRepository extends Repository
{
    /**
     * @param $email
     *
     * @return Contributor|null
     */
    public function findByEmail($email)
    {
        $qb = $this->createQueryBuilder('c')
            ->select()
            ->andWhere('c.email = :email')
            ->orWhere('c.gitEmails LIKE :emailLike')
            ->setParameter('email', $email)
            ->setParameter('emailLike', '%'.$email.'%');

        $result = $qb->getQuery()->getOneOrNullResult();

        return $result;
    }

    /**
     * @param $name
     *
     * @return Contributor[]
     */
    public function findByName($name)
    {
        $qb = $this->createQueryBuilder('c')
            ->select()
            ->andWhere('c.name = :name')
            ->orWhere('c.gitNames LIKE :nameLike')
            ->setParameter('name', $name)
            ->setParameter('nameLike', '%'.$name.'%');

        $result = $qb->getQuery()->getResult();

        return $result;
    }

    public function getDoubles()
    {
        $qb = $this->createQueryBuilder('c')
            ->select('c.name, COUNT(c.id) as cnt')
            ->groupBy('c.name')
            //->having('COUNT(c.id) > 1')
        ;

        $result = $qb->getQuery()->getArrayResult();

        $doubles = [];
        foreach ($result as $item) {
            $doubles[$item['name']] = (int)$item['cnt'];
        }

        return $doubles;
    }

    public function getContributorNames()
    {
        $qb = $this->createQueryBuilder('c')
            ->select('c.id', 'c.email', 'c.name', 'c.gitNames');

        $result = $qb->getQuery()->getArrayResult();

        $names = [];
        foreach ($result as $item) {
            $id = $item['id'];
            $names[$id] = [
                'email' => $item['email'],
                'names' => array_filter(array_merge([$item['name']], $item['gitNames'])),
            ];
        }

        return $names;
    }

    /**
     * @return Contributor[]
     */
    public function findWithSensiolabsLogin()
    {
        $qb = $this->createQueryBuilder('c')
            ->select()
            ->where('c.sensiolabsLogin != \'\'')
        ;

        $result = $qb->getQuery()->getResult();

        return $result;
    }

    /**
     * @return Contributor[]
     */
    public function findWithoutGithubLogin($limit)
    {
        $qb = $this->createQueryBuilder('c')
            ->select()
            ->where('c.githubLogin = \'\'')
            ->setMaxResults($limit)
        ;

        $result = $qb->getQuery()->getResult();

        return $result;
    }

    /**
     * @return Contributor[]
     */
    public function findWithoutLocation($limit)
    {
        $qb = $this->createQueryBuilder('c')
            ->select()
            ->where('c.githubLogin != \'\'')
            //->where('c.sensiolabsCountry = \'\'')
            ->setMaxResults($limit)
        ;

        $result = $qb->getQuery()->getResult();

        return $result;
    }
}

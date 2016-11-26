<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Contributor;
use Doctrine\ORM\Query\ResultSetMapping;

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
     * @param $login
     *
     * @return Contributor|null
     */
    public function findByLogin($login)
    {
        $qb = $this->createQueryBuilder('c')
            ->select()
            ->andWhere('c.githubLogin = :login')
            ->setParameter('login', $login);

        $result = $qb->getQuery()->getOneOrNullResult();

        return $result;
    }

    /**
     * @param $id
     *
     * @return Contributor|null
     */
    public function findByGithubId($id)
    {
        $qb = $this->createQueryBuilder('c')
            ->select()
            ->andWhere('c.githubId = :id')
            ->setParameter('id', $id);

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

    /**
     * @return array
     */
    public function getContributionsPerCountry()
    {
        $rsm = new ResultSetMapping();
        $rsm
            ->addScalarResult('id', 'id')
            ->addScalarResult('iso_1', 'iso_1')
            ->addScalarResult('iso_2', 'iso_2');

        $query = $this
            ->getEntityManager()
            ->createNativeQuery('select c.id, cn1.iso2 as iso_1, cn2.iso2 as iso_2
                                    from contributor c
                                      left join sensiolabs_user s on s.contributor_id = c.id
                                      left join country cn1 on cn1.name = c.country
                                      left join country cn2 on cn2.name = s.country
                                    where
                                      (c.country != \'\' OR s.country != \'\') and
                                      exists (
                                          select cn.id
                                          from contribution cn
                                          where cn.project_id = 2 and cn.contributor_id = c.id
                                      )', $rsm);

        $result = $query->getResult();

        return $result;
    }
}

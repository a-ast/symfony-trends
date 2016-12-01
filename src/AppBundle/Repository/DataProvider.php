<?php


namespace AppBundle\Repository;

use Doctrine\DBAL\Connection;

class DataProvider
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * Constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param string $dataSource
     * @param array $criteria
     *
     * @return array
     */
    public function getData($dataSource, $criteria)
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder
            ->select('*')
            ->from($dataSource);

        foreach ($criteria as $criteriaKey => $criteriaValue) {
            $queryBuilder
                ->andWhere(sprintf('%1$s = :%1$s', $criteriaKey))
                ->setParameter($criteriaKey, $criteriaValue);
        }

        $result = $queryBuilder->execute()->fetchAll();

        return $result;
    }
}

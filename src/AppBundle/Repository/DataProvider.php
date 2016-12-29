<?php


namespace AppBundle\Repository;

use AppBundle\Util\StringUtils;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use RuntimeException;

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
     * @param integer $limit
     *
     * @return array
     */
    public function getData($dataSource, $criteria, $limit = null)
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        if ($this->isDataSourceDataView($dataSource)) {
            $this->configureQueryBuilderForDataView($queryBuilder, $dataSource, $criteria);
        } elseif ($this->isDataSourceDataFunction($dataSource)) {
            $this->configureQueryBuilderForDataFunction($queryBuilder, $dataSource, $criteria);
        } else {
            throw new RuntimeException(sprintf('Unknown type of the data source: %s', $dataSource));
        }

        if (null !== $limit) {
            $queryBuilder->setMaxResults($limit);
        }

        $result = $queryBuilder->execute()->fetchAll();

        return $result;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param string $dataSource
     * @param array $criteria
     */
    private function configureQueryBuilderForDataView(QueryBuilder $queryBuilder, $dataSource, $criteria)
    {
        $queryBuilder
            ->select('*')
            ->from($dataSource);

        foreach ($criteria as $criteriaKey => $criteriaValue) {
            $queryBuilder
                ->andWhere(sprintf('%1$s = :%1$s', $criteriaKey))
                ->setParameter($criteriaKey, $criteriaValue);
        }
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param string $dataSource
     * @param array $criteria
     */
    private function configureQueryBuilderForDataFunction(QueryBuilder $queryBuilder, $dataSource, $criteria)
    {
        $parameters = [];
        foreach ($criteria as $criteriaKey => $criteriaValue) {
            $parameters[] = ':'.$criteriaKey;
        }

        $dataSource = sprintf('%s(%s)', $dataSource, implode(', ', $parameters));

        $queryBuilder
            ->select('*')
            ->from($dataSource);

        foreach ($criteria as $criteriaKey => $criteriaValue) {
            $queryBuilder
                ->setParameter($criteriaKey, $criteriaValue);
        }
    }

    /**
     * @param string $dataSource
     *
     * @return bool
     */
    private function isDataSourceDataView($dataSource)
    {
        return StringUtils::startsWith($dataSource, 'vw_');
    }

    /**
     * @param string $dataSource
     *
     * @return bool
     */
    private function isDataSourceDataFunction($dataSource)
    {
        return StringUtils::startsWith($dataSource, 'fn_');
    }
}

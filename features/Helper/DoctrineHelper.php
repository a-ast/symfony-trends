<?php


namespace features\Helper;

use Aa\ArrayDiff\Calculator;
use Aa\ArrayDiff\Matcher\SimpleMatcher;
use Doctrine\Common\DataFixtures\Purger\PurgerInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;
use Exception;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Traversable;

class DoctrineHelper
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var PurgerInterface
     */
    private $purger;

    public function __construct(EntityManager $em, PurgerInterface $purger)
    {
        $this->em = $em;
        $this->purger = $purger;
    }

    /**
     * Purge all entities
     */
    public function purgeEntities()
    {
        $this->purger->purge();
        $this->updatePostgresqlSequences();
    }

    /**
     * Create and store entities
     *
     * @param string $entityClass
     * @param Traversable $records
     */
    public function createEntities($entityClass, Traversable $records)
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        foreach ($records as $record) {

            $entity = new $entityClass();

            foreach ($record as $propertyName => $propertyValue) {
                $propertyAccessor->setValue($entity, $propertyName, $propertyValue);
            }

            $this->em->persist($entity);
        }

        $this->em->flush();
    }

    /**
     * Compare existing entities with given records
     *
     * @param string $entityClass
     * @param Traversable $records
     *
     * @throws Exception
     */
    public function checkEntities($entityClass, Traversable $records)
    {
        $queryBuilder = $this->em->getRepository($entityClass)->createQueryBuilder('data');
        $actualData = $queryBuilder->getQuery()->getArrayResult();

        foreach ($actualData as &$actualRow) {
            foreach ($actualRow as $key => $value) {
                if (is_array($value)) {
                    $actualRow[$key] = implode(',', $value);
                }
            }
        }

        $expectedData = [];
        foreach ($records as $expectedRow) {
            $expectedData[] = $expectedRow;
        }

        $calc = new Calculator(new SimpleMatcher());
        $diff = $calc->calculateDiff($expectedData, $actualData);

        if (0 < count($diff->getMissing()) + count($diff->getUnmatched())) {
            throw new Exception('Expected entities are different from actual entities:'.PHP_EOL.$diff->toString());
        }
    }

    private function updatePostgresqlSequences()
    {
        $query = $this->em->createNativeQuery('ALTER SEQUENCE project_id_seq RESTART;', new ResultSetMapping());
        $query->execute();

        $query = $this->em->createNativeQuery('ALTER SEQUENCE contributor_id_seq RESTART;', new ResultSetMapping());
        $query->execute();

        $query = $this->em->createNativeQuery('ALTER SEQUENCE contribution_id_seq RESTART;', new ResultSetMapping());
        $query->execute();
    }

}

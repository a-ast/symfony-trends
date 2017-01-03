<?php

use Aa\ArrayDiff\Matcher\SimpleMatcher;
use AppBundle\Aggregator\GithubCommitHistory;
use AppBundle\Model\GithubCommit;
use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\TableNode;
use Doctrine\Common\DataFixtures\Purger\PurgerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Query\ResultSetMapping;
use features\Fake\ClientAdapterFake;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Aa\ArrayDiff\Calculator;

/**
 * Defines application features from the specific context.
 */
class AggregatorFeatureContext implements Context
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    /**
     * @var PurgerInterface
     */
    private $purger;
    /**
     * @var GithubCommitHistory
     */
    private $aggregator;
    /**
     * @var ClientAdapterFake
     */
    private $clientAdapter;

    /**
     * Initializes context.
     *
     * @param ObjectManager $em
     * @param PurgerInterface $purger
     * @param GithubCommitHistory $aggregator
     * @param ClientAdapterFake $client
     */
    public function __construct(ObjectManager $em, PurgerInterface $purger, GithubCommitHistory $aggregator, ClientAdapterFake $client)
    {
        $this->em = $em;
        $this->purger = $purger;
        $this->aggregator = $aggregator;
        $this->clientAdapter = $client;
    }

    /**
     * @BeforeScenario
     *
     * @param BeforeScenarioScope $event
     */
    public function before(BeforeScenarioScope $event)
    {
        $this->purger->purge();
        $this->updatePostgresqlSequences();
    }

    /**
     * @Given there are :entityClass entities:
     *
     * @param $entityClass
     * @param TableNode $records
     */
    public function createEntities($entityClass, TableNode $records)
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
     * @Then I should see :entityClass entities:
     *
     * @param string $entityClass
     * @param TableNode $records
     *
     * @throws Exception
     */
    public function checkEntities($entityClass, TableNode $records)
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

    /**
     * @Given I request commits:
     */
    public function iRequestCommits(TableNode $commits)
    {
        foreach ($commits as $commitData) {
            $commit = GithubCommit::createFromArray($commitData);
            $this->clientAdapter->addCommit($commit);
        }
    }

    /**
     * @When I aggregate commits
     */
    public function iAggregateCommits()
    {
        $this->aggregator->aggregate(['project_id' => 1]);
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

    /**
     * @Given /^there are AppBundle:Project entities:$/
     */
    public function thereAreAppBundleProjectEntities(TableNode $table)
    {
        throw new PendingException();
    }
}


<?php

use AppBundle\Aggregator\GithubCommitHistory;
use AppBundle\Model\GithubCommit;
use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\TableNode;
use features\Fake\ClientAdapterFake;
use features\Helper\DoctrineHelper;

/**
 * Defines application features from the specific context.
 */
class AggregatorFeatureContext implements Context
{
    /**
     * @var GithubCommitHistory
     */
    private $aggregator;

    /**
     * @var ClientAdapterFake
     */
    private $clientAdapter;

    /**
     * @var DoctrineHelper
     */
    private $doctrineHelper;

    /**
     * Initializes context.
     *
     * @param GithubCommitHistory $aggregator
     * @param ClientAdapterFake $client
     * @param DoctrineHelper $doctrineHelper
     */
    public function __construct(GithubCommitHistory $aggregator, ClientAdapterFake $client, DoctrineHelper $doctrineHelper)
    {
        $this->aggregator = $aggregator;
        $this->clientAdapter = $client;
        $this->doctrineHelper = $doctrineHelper;
    }

    /**
     * @BeforeScenario
     *
     * @param BeforeScenarioScope $event
     */
    public function before(BeforeScenarioScope $event)
    {
        $this->doctrineHelper->purgeEntities();
    }

    /**
     * @Given there are :entityClass entities:
     *
     * @param string $entityClass
     * @param TableNode $records
     */
    public function createEntities($entityClass, TableNode $records)
    {
        $this->doctrineHelper->createEntities($entityClass, $records);
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
        $this->doctrineHelper->checkEntities($entityClass, $records);
    }

    /**
     * @Given I request commits:
     *
     * @param TableNode $commits
     */
    public function iRequestCommits(TableNode $commits)
    {
        foreach ($commits as $commitData) {
            $commit = new GithubCommit($commitData);
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
}

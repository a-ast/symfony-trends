<?php

use AppBundle\Aggregator\AggregatorRegistry;
use AppBundle\Repository\ProjectRepository;
use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\TableNode;
use features\Helper\ApiCollection;
use features\Helper\DoctrineHelper;

/**
 * Defines application features from the specific context.
 */
class AggregatorFeatureContext implements Context
{
    /**
     * @var DoctrineHelper
     */
    private $doctrineHelper;

    /**
     * @var ProjectRepository
     */
    private $projectRepository;

    /**
     * @var AggregatorRegistry
     */
    private $aggregatorRegistry;

    /**
     * @var array
     */
    private $apis;

    /**
     * Initializes context.
     *
     * @param AggregatorRegistry $aggregatorRegistry
     * @param DoctrineHelper $doctrineHelper
     * @param ProjectRepository $projectRepository
     * @param ApiCollection $apis
     */
    public function __construct(AggregatorRegistry $aggregatorRegistry,
        DoctrineHelper $doctrineHelper, ProjectRepository $projectRepository,
        ApiCollection $apis)
    {
        $this->aggregatorRegistry = $aggregatorRegistry;
        $this->doctrineHelper = $doctrineHelper;
        $this->projectRepository = $projectRepository;
        $this->apis = $apis;
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
     * @Given :apiName API returns :dataType data:
     */
    public function apiReturnsData($apiName, $dataType, TableNode $records)
    {
        $api = $this->apis->get($apiName);

        foreach ($records as $record) {
            $data = $this->replaceNulls($record);

            $api->addData($dataType, $data);
        }
    }

    /**
     * @When I aggregate :aggregatorAlias for project :projectId
     *
     * @param int $projectId
     */
    public function iAggregate($aggregatorAlias, $projectId)
    {
        $project = $this->projectRepository->find($projectId);

        $aggregator = $this->aggregatorRegistry->get($aggregatorAlias);

        $aggregator->aggregate($project, []);
    }

    private function replaceNulls(array $data)
    {
        return array_map(function($item) {
                            return '~' === $item ? null : $item;
                         },
                         $data);
    }

}


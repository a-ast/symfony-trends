<?php

use Aa\ATrends\Aggregator\AggregatorRegistry;
use Aa\ATrends\Aggregator\Options\Options;
use Aa\ATrends\Aggregator\Options\OptionsInterface;
use Aa\ATrends\Aggregator\Report\ReportAwareInterface;
use Aa\ATrends\Aggregator\Report\ReportInterface;
use Aa\ATrends\Repository\ProjectRepository;
use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\TableNode;
use features\Aa\ATrends\Helper\ApiCollection;
use features\Aa\ATrends\Helper\DoctrineHelper;
use Webmozart\Assert\Assert;

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
     * @var ReportInterface
     */
    private $report;

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
            $data = $this->processTableRow($record);

            $api->addData($dataType, $data);
        }
    }

    /**
     * @When I aggregate :aggregatorAlias for project :projectId
     *
     * @param int|null $projectId
     */
    public function iAggregateForProject($aggregatorAlias, $projectId)
    {
        $aggregator = $this->aggregatorRegistry->get($aggregatorAlias);

        if (null !== $projectId) {
            $project = $this->projectRepository->find($projectId);
            $aggregator->setProject($project);
        }

        $aggregator->aggregate(new Options(OptionsInterface::SINCE_LAST_UPDATE));

        if ($aggregator instanceof ReportAwareInterface) {
            $this->report = $aggregator->getReport();
        }
    }

    /**
     * @When I aggregate :aggregatorAlias
     */
    public function iAggregate($aggregatorAlias)
    {
        $this->iAggregateForProject($aggregatorAlias, null);
    }

    /**
     * @Then I should see the report:
     */
    public function iShouldSeeTheReport(TableNode $reportTable)
    {
        $expectedReport = $reportTable->getRows();

        $report = [
            ['processedItemCount', $this->report->getProcessedItemCount()]
        ];

        Assert::eq($expectedReport, $report);
    }

    private function processTableRow(array $data)
    {
        $helper = $this->doctrineHelper;

        return array_map(function($item) use ($helper) {
                            return $helper->processTableCellValue($item);
                         },
                         $data);
    }

}


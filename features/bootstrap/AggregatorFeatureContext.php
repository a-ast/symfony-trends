<?php

use AppBundle\Aggregator\AggregatorRegistry;
use AppBundle\Aggregator\GithubCommitHistory;
use AppBundle\Model\GithubCommit;
use AppBundle\Model\GithubUser;
use AppBundle\Repository\ProjectRepository;
use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\TableNode;
use features\Fake\ClientAdapterFake;
use features\Fake\GeocodingApi;
use features\Helper\DoctrineHelper;
use Http\Mock\Client;

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
     * @var ProjectRepository
     */
    private $projectRepository;
    /**
     * @var AggregatorRegistry
     */
    private $aggregatorRegistry;

    /**
     * @var Client
     */
    private $geocoder;

    /**
     * Initializes context.
     *
     * @param GithubCommitHistory $aggregator
     * @param AggregatorRegistry $aggregatorRegistry
     * @param ClientAdapterFake $client
     * @param Client $geocoder
     * @param DoctrineHelper $doctrineHelper
     * @param ProjectRepository $projectRepository
     */
    public function __construct(GithubCommitHistory $aggregator, AggregatorRegistry $aggregatorRegistry,
        ClientAdapterFake $client, GeocodingApi $geocoder, DoctrineHelper $doctrineHelper, ProjectRepository $projectRepository)
    {
        $this->aggregator = $aggregator;
        $this->aggregatorRegistry = $aggregatorRegistry;
        $this->clientAdapter = $client;
        $this->doctrineHelper = $doctrineHelper;
        $this->projectRepository = $projectRepository;
        $this->geocoder = $geocoder;
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
     * @Given Github returns commits:
     *
     * @param TableNode $commits
     */
    public function githubReturnsCommits(TableNode $commits)
    {
        foreach ($commits as $commitData) {
            $commit = new GithubCommit($this->replaceNulls($commitData));
            $this->clientAdapter->addCommit($commit);
        }
    }

    /**
     * @Given Github returns users:
     */
    public function githubReturnsUsers(TableNode $users)
    {
        foreach ($users as $userData) {
            $user = GithubUser::createFromGithubResponseData($this->replaceNulls($userData));
            $this->clientAdapter->addUser($userData['login'], $user);
        }
    }

    /**
     * @Given Geocoding API returns :dataType data:
     */
    public function geocodingApiReturnsData($dataType, TableNode $records)
    {
        foreach ($records as $record) {
            $data = $this->replaceNulls($record);

            $this->geocoder->addData($dataType, $data);
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


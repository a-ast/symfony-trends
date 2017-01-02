<?php

use AppBundle\Aggregator\GithubCommitHistory;
use AppBundle\Entity\Contributor;
use AppBundle\Entity\Project;
use AppBundle\Model\GithubCommit;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Doctrine\Common\DataFixtures\Purger\PurgerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Query\ResultSetMapping;
use features\Fake\ClientAdapterFake;

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
     * @Given I have existing projects:
     *
     * @param TableNode $projects
     */
    public function existingProjects(TableNode $projects)
    {
        $this->purger->purge();

        $query = $this->em->createNativeQuery('ALTER SEQUENCE project_id_seq RESTART;', new ResultSetMapping());
        $query->execute();

        foreach ($projects as $projectData) {

            $project = new Project();
            $project
                ->setName($projectData['name'])
                ->setLabel($projectData['label'])
                ->setGithubPath($projectData['path'])
                ->setColor($projectData['color']);

            $this->em->persist($project);
        }

        $this->em->flush();
    }

    /**
     * @Given I request commits:
     */
    public function givenApiCommits(TableNode $commits)
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

    /**
     * @Then I should see these contributors in the database:
     */
    public function iShouldSeeTheseContributorsInTheDatabase(TableNode $expected)
    {
        $queryBuilder = $this->em->getRepository(Contributor::class)->createQueryBuilder('data');
        $actualData = $queryBuilder->getQuery()->getArrayResult();

        foreach ($actualData as &$actualRow) {
            foreach ($actualRow as $key => $value) {
                if (is_array($value)) {
                    $actualRow[$key] = implode(',', $value);
                }
            }
        }

        $expectedData = [];
        foreach ($expected as $expectedRow) {
            $expectedData[] = $expectedRow;
        }

        $missing = [];
        $different = [];

        foreach ($expectedData as $index => $expectedRow) {
            $actualRow = $actualData[$index];

            foreach ($expectedRow as $key => $value) {

                if (!isset($actualRow[$key])) {
                    $missing[] = $key;

                    continue;
                }

                if ($actualRow[$key] != $expectedRow[$key]) {
                    $different[] = sprintf('Expected: [%s], actual [%s]', $actualRow[$key], $expectedRow[$key]);
                }
            }
        }

        if (0 !== count($missing) || 0 !== count($different)) {
            throw new Exception(implode(PHP_EOL, $missing).PHP_EOL.implode(PHP_EOL, $different));
        }

    }
}

